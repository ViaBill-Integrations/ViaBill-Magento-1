<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_ViabillController
 */
class Viabill_Payment_ViabillController extends Mage_Core_Controller_Front_Action
{
    const VIABILL_STATUS_APPROVED = 'APPROVED';
    const VIABILL_STATUS_CANCELLED = 'CANCELLED';
    const WARNING_MESSAGE = 'This order was placed in ViaBill Test Mode and should not be shipped! If you have any questions about this order, please contact <a href=https://viabill.com/ target=_blank>ViaBill Support</a>.';
    const CANCEL_MESSAGE = 'Payment cancelled from Viabill.';

    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $id = $session->getQuoteId();
        if ($id) {
            $session->setViabillQuoteId($id);
            $this->getResponse()->setBody($this->getLayout()->createBlock('viabill/redirect')->toHtml());
            $session->unsQuoteId();
        } else {
            $this->_redirect('*/*/');
        }
    }

    public function successAction()
    {
        $this->_redirect('checkout/onepage/success');
        return;
    }

    public function callbackAction()
    {
        $requestData = Mage::app()->getRequest()->getParams();
        $entityBody = file_get_contents('php://input');
        if (!empty($entityBody)) {
            $requestData = (array) json_decode($entityBody, true);
        }

        Mage::getModel('viabill/logger')->debug(json_encode($requestData));

        if ($this->isValidRequest($requestData) && $this->isValidSignature($requestData)) {
            $orderIncrementId = $requestData['orderNumber'];
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
            $this->processOrder($order, $requestData);
            $this->getResponse()->setHeader('HTTP/1.0', '204', true);
        } else {
            $this->getResponse()->setHeader('HTTP/1.0', '500', true);
        }
    }

    public function cancelAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $id = $session->getQuoteId();
        if ($id) {
            $checkoutHelper = Mage::helper('viabill/checkout');
            $checkoutHelper->cancelCurrentOrder(self::CANCEL_MESSAGE);
            $checkoutHelper->restoreQuote();
        }

        $this->_redirect('checkout/cart');
    }

    /**
     * @param $requestData
     *
     * @return bool
     */
    protected function isValidSignature($requestData)
    {
        $requestSignature = $requestData['signature'];
        $signatureHelper = Mage::helper('viabill/signature');
        $realSignature = $signatureHelper->getSignature(
            $requestData,
            Viabill_Payment_Helper_Signature::CALLBACK_REQUEST_SIGNATURE_PATTERN
        );
        return $requestSignature === $realSignature;
    }

    /**
     * Check if all required fields present
     *
     * @param $requestData
     *
     * @return bool
     */
    protected function isValidRequest($requestData)
    {
        $structure = array(
            'transaction',
            'orderNumber',
            'amount',
            'currency',
            'status',
            'time',
            'signature'
        );
        return empty(array_diff($structure, array_keys($requestData)));
    }

    /**
     * @param $order
     * @param $requestData
     */
    protected function processOrder($order, $requestData)
    {
        if ($requestData['status'] === self::VIABILL_STATUS_APPROVED
            && $order->getState() === Mage_Sales_Model_Order::STATE_NEW
        ) {
            $payment = $order->getPayment();
            $payment->setAdditionalData(json_encode($requestData));
            $payment->setTransactionId($requestData['transaction']);
            $payment->setIsTransactionClosed(false);
            $payment->registerAuthorizationNotification($requestData['amount']);

            $viabillConfig = Mage::getSingleton('viabill/config');

            $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
            $status = $viabillConfig->getConfigData('order_status_after_authorization');
            if (!$status) {
                /**
                 * @var Mage_Sales_Model_Order_Config $orderConfig
                 */
                $orderConfig = Mage::getSingleton('sales/order_config');
                $status = $orderConfig->getStateDefaultStatus($state);
            }

            $order->sendNewOrderEmail();
            $order->setEmailSent(true);

            $order->setState($state);
            $order->setStatus($status);
            $order->save();

            $isTest = (bool) $viabillConfig->getConfigData('test_transaction');

            if ($isTest == true) {
                $savedOrder = Mage::getModel('sales/order')->load($order->getId());
                $savedOrder->addStatusHistoryComment(self::WARNING_MESSAGE);
                $savedOrder->setCustomerNote(self::WARNING_MESSAGE);
                $savedOrder->save();
            }

            $authorizeAndCapture = $viabillConfig->getConfigData('payment_action');
            if ($authorizeAndCapture == Mage_Paygate_Model_Authorizenet::ACTION_AUTHORIZE_CAPTURE) {
                $placedOrder = Mage::getModel('sales/order')->load($order->getId());
                $this->captureOrder($placedOrder);
            }
        } elseif ($requestData['status'] === self::VIABILL_STATUS_CANCELLED) {
            $order->cancel();
            $order->addStatusHistoryComment(self::CANCEL_MESSAGE);
            $order->save();
        }
    }

    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @throws Exception
     */
    private function captureOrder($order)
    {
        $invoice = $order->prepareInvoice();
        $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
        $invoice->register();
        $invoice->setEmailSent(true);
        $invoice->getOrder()->setCustomerNoteNotify(true);
        $invoice->sendEmail(true, '');

        $transaction = Mage::getModel('core/resource_transaction');
        $transaction->addObject($invoice);
        $transaction->addObject($invoice->getOrder());
        $transaction->save();

        $savedOrder = Mage::getModel('sales/order')->load($order->getId());
        $viabillConfig = Mage::getSingleton('viabill/config');
        $state = Mage_Sales_Model_Order::STATE_PROCESSING;
        $status = $viabillConfig->getConfigData('order_status_after_capture');
        if (!$status) {
            /**
             * @var Mage_Sales_Model_Order_Config $orderConfig
             */
            $orderConfig = Mage::getSingleton('sales/order_config');
            $status = $orderConfig->getStateDefaultStatus($state);
        }

        $savedOrder->setState($state);
        $savedOrder->setStatus($status);
        $savedOrder->save();
    }
}
