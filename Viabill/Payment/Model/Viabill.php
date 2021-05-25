<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Model_Viabill
 */
class Viabill_Payment_Model_Viabill extends Mage_Payment_Model_Method_Abstract
{
    const PAYMENT_TYPE_AUTH = 'AUTHORIZATION';
    const PAYMENT_TYPE_SALE = 'SALE';
    const VIABILL_PROTOCOL = '3.1';

    /**
     * @var string
     */
    const VIABILL_UNIQUE_IDENTITY = 'viabill';

    /**
     * @var string
     */
    protected $_code = self::VIABILL_UNIQUE_IDENTITY;
    /**
     * @var string
     */
    protected $_formBlockType = 'viabill/form';
    /**
     * @var string
     */
    protected $_infoBlockType = 'viabill/info';
    /**
     * @var bool
     */
    protected $_isInitializeNeeded      = true;
    /**
     * @var bool
     */
    protected $_canCapture              = true;
    /**
     * @var bool
     */
    protected $_canCapturePartial       = true;
    /**
     * @var bool
     */
    protected $_canRefund               = true;
    /**
     * @var bool
     */
    protected $_canRefundInvoicePartial = true;
    /**
     * @var bool
     */
    protected $_canVoid                 = true;
    /**
     * @var null
     */
    protected $_storeId                 = null;

    /**
     * @var array
     */
    protected $_allowCurrencyCode = array(
        'DKK', 'NOK', 'EUR', 'USD'
    );

    /**
     * @var array
     */
    protected $_viabillCountryCodes = array(
        'DK' => 'da',
        'NO' => 'no',
        'ES' => 'es',
        'US' => 'us'
    );

    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    /**
     * Using internal pages for input payment data
     *
     * @return bool
     */
    public function canUseInternal()
    {
        return false;
    }

    /**
     * Using for multiple shipping address
     *
     * @return bool
     */
    public function canUseForMultishipping()
    {
        return false;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('viabill/viabill_form', $name)
            ->setMethod('viabill')
            ->setPayment($this->getPayment())
            ->setTemplate('viabill/payment/form.phtml');

        return $block;
    }

    /**
     * @return string
     */
    private function getQuoteBaseCurrencyCode()
    {
        $quote = null;
        $paymentInfo = $this->getInfoInstance();
        if ($paymentInfo) {
            if ($paymentInfo instanceof Mage_Sales_Model_Order_Payment) {
                $quoteId = $paymentInfo->getOrder()->getQuoteId();
                $quote = Mage::getModel('sales/quote')->load($quoteId);
            } else {
                $quote = $paymentInfo->getQuote();
            }
        }
        if (!$quote) {
            $quote = $this->getQuote();
        }

        return $quote->getBaseCurrencyCode();
    }

    /**
     * @return $this
     */
    public function validate()
    {
        parent::validate();
        $currency_code = $this->getQuoteBaseCurrencyCode();
        if (!in_array($currency_code, $this->_allowCurrencyCode)) {
            Mage::throwException(Mage::helper('viabill')->__('Selected currency code is not compatabile with Viabill') . '(' . $currency_code . ')');
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('viabill/viabill/redirect', array('_secure' => $this->isFrontendSecure()));
    }

    /**
     * @return string
     */
    protected function getFailureURL()
    {
        $url = Mage::getUrl($this->getConfigData('url_cancel'), array('_secure' => $this->isFrontendSecure()));

        return $url;
    }

    /**
     * @return string
     */
    protected function getAcceptURL()
    {
        $url = Mage::getUrl($this->getConfigData('url_success'), array('_secure' => $this->isFrontendSecure()));

        return $url;
    }

    /**
     * @return mixed|string
     */
    protected function getCallbackURL()
    {
        $url = Mage::getUrl($this->getConfigData('url_callback'), array('_secure' => $this->isFrontendSecure()));
        if ($this->getConfigData('debug')) {
            $externalUrl = $this->getConfigData('callback_debug_base_url');
            if (!empty($externalUrl)) {
                $url = str_replace(
                    Mage::getBaseUrl(),
                    $externalUrl,
                    $url
                );
                if ($this->getConfigData('callback_xdebug_session_enable')) {
                    $url .= '?XDEBUG_SESSION_START=1';
                }
            }
        }

        return $url;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return Mage::helper('viabill/url')->getApiUrl(Viabill_Payment_Helper_Url::ENDPOINT_URL_ORDER);
    }

    /**
     * @return mixed
     */
    public function getCancelUrl()
    {
        return Mage::helper('viabill/url')->getApiUrl(Viabill_Payment_Helper_Url::ENDPOINT_URL_CANCEL);
    }

    /**
     * @return string
     */
    public function getFinalSuccessUrl()
    {
        return Mage::getUrl('checkout/onepage/success');
    }

    /**
     * @return string
     */
    public function getFinalFailureURL()
    {
        return Mage::getUrl('checkout/onepage/failure');
    }

    /**
     * @return array
     */
    public function getCheckoutFormFields()
    {
        $helper = Mage::helper('viabill');
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($this->getCheckout()->getLastRealOrderId());
        $isTest = (bool) $this->getConfigData('test_transaction');

        $orderId = $order->getRealOrderId();

        $fields = array(
            'protocol' => self::VIABILL_PROTOCOL,
            'apikey' => $helper->getViabillKey(),
            'amount' => $order->getTotalDue(),
            'currency' => $order->getOrderCurrency()->getCurrencyCode(),
            'transaction' => $helper->generateViabillTransactionId($orderId),
            'order_number' => $order->getRealOrderId(),
            'success_url' => $this->getAcceptURL(),
            'cancel_url' => $this->getFailureURL(),
            'callback_url' => $this->getCallbackURL(),
            'test' => $isTest ? 'true' : 'false'
        );

        $signaturePattern = Viabill_Payment_Helper_Signature::NEW_ORDER_SIGNATURE_PATTERN;
        $signature = Mage::helper('viabill/signature')->getSignature($fields, $signaturePattern);
        $fields['sha256check'] = $signature;

        return $fields;
    }

    /**
     * @return bool
     */
    public function isInitializeNeeded()
    {
        return $this->_isInitializeNeeded;
    }

    /**
     * @param string $paymentAction
     * @param object $stateObject
     */
    public function initialize($paymentAction, $stateObject)
    {
        $state = Mage_Sales_Model_Order::STATE_NEW;

        /**
 * @var Mage_Sales_Model_Order_Config $orderConfig 
*/
        $orderConfig = Mage::getSingleton('sales/order_config');

        $status = $this->getConfigData('order_status_before_authorization');
        if (!$status) {
            $status = $orderConfig->getStateDefaultStatus($state);
        }

        $stateObject->setState($state);
        $stateObject->setStatus($status);
        $stateObject->setIsNotified(false);
    }

    /**
     * @param Varien_Object $payment
     * @param float         $amount
     * @return $this
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        return $this;
    }

    /**
     * @return bool
     */
    public function isFrontendSecure()
    {
        return (bool)Mage::getStoreConfig('web/secure/use_in_frontend');
    }

    /**
     * @param Varien_Object $payment
     * @param float         $inamount
     * @return $this
     */
    public function capture(Varien_Object $payment, $inamount)
    {
        /**
 * @var Viabill_Payment_Helper_Data $helper 
*/
        $helper = Mage::helper('viabill');
        /**
 * @var Mage_Sales_Model_Order $order 
*/
        $order = $payment->getOrder();

        /**
 * @var Viabill_Payment_Model_Gateway $gateway 
*/
        $gateway = Mage::getModel('viabill/gateway');
        try{
            $gateway->captureOrder($order, $inamount);
            Mage::getSingleton('core/session')->addSuccess(
                $helper->__('The order has been successfully captured.')
            );
            $order->save();
            return $this;
        } catch (Viabill_Payment_Model_Exception_ViabillException $e) {
            Mage::getSingleton('core/session')->addError(
                $helper->__($e->getMessage())
            );
        } catch (Exception $e) {
            // die($e->getMessage());
            Mage::getSingleton('core/session')->addError(
                $helper->__('An error occurred during order capture. Please try again later.')
            );
            Mage::getModel('viabill/logger')->critical($e->getMessage());
        }

        throw $e;
    }

    /**
     * @param Varien_Object $payment
     *
     * @return $this
     */
    public function cancel(Varien_Object $payment)
    {
        /**
 * @var Viabill_Payment_Helper_Data $helper 
*/
        $helper = Mage::helper('viabill');
        $order = $payment->getOrder();

        /**
 * @var Viabill_Payment_Model_Gateway $gateway 
*/
        $gateway = Mage::getModel('viabill/gateway');
        try{
            $gateway->cancelOrder($order);
            $order->save();
            return $this;
        } catch (Viabill_Payment_Model_Exception_ViabillException $e) {
            Mage::getSingleton('core/session')->addError(
                $helper->__($e->getMessage())
            );
        } catch (Exception $e) {
            Mage::getModel('viabill/logger')->critical($e->getMessage());
            Mage::getSingleton('core/session')->addError(
                $helper->__('An error occurred during order cancel. Please try again later.')
            );
        }

        throw $e;
    }

    /**
     * @param Varien_Object $payment
     * @param float         $inamount
     * @return $this
     */
    public function refund(Varien_Object $payment, $inamount)
    {

        /**
 * @var Viabill_Payment_Helper_Data $helper 
*/
        $helper = Mage::helper('viabill');
        /**
 * @var Mage_Sales_Model_Order $order 
*/
        $order = $payment->getOrder();

        /**
 * @var Viabill_Payment_Model_Gateway $gateway 
*/
        $gateway = Mage::getModel('viabill/gateway');
        try{
            $gateway->refundOrder($order, $inamount);
            $order->save();
            Mage::getSingleton('core/session')->addSuccess(
                $helper->__('The order has been successfully refunded.')
            );
            return $this;
        } catch (Viabill_Payment_Model_Exception_ViabillException $e) {
            Mage::getSingleton('core/session')->addError(
                $helper->__($e->getMessage())
            );
        } catch (Exception $e) {
            Mage::getModel('viabill/logger')->critical($e->getMessage());
            Mage::getSingleton('core/session')->addError(
                $helper->__('An error occurred during order refund. Please try again later.')
            );
        }

        throw $e;
    }

    /**
     * Retrieve information from payment configuration
     *
     * @param string                                $field
     * @param int|string|null|Mage_Core_Model_Store $storeId
     *
     * @return mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->getStore();
        }
        $path = 'payment/'.$this->getCode().'_config_form/'.$field;
        $configValue = Mage::getStoreConfig($path, $storeId);
        if (empty($configValue)) {
            return parent::getConfigData($field, $storeId);
        }

        return $configValue;
    }
}
