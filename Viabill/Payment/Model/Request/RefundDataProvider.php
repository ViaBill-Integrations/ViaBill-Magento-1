<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Model_Request_RefundDataProvider
 */
class Viabill_Payment_Model_Request_RefundDataProvider
{
    /**
     * @param array $commandSubject
     *
     * @return array
     */
    public function getFields(&$commandSubject = array())
    {
        $order = $commandSubject['order'];
        $authTransactionId = $order->getPayment()->getAuthorizationTransaction()->getParentTxnId();
        if (empty($authTransactionId)) {
            Mage::throwException(
                Mage::helper('viabill')->__("This order hasn't been authorized so it can not be refunded.")
            );
        }

        $helper = Mage::helper('viabill');
        $fields = array(
            'id' => $authTransactionId,
            'apikey' => $helper->getViabillKey(),
            'amount' => $commandSubject['amount'],
            'currency' => $order->getOrderCurrency()->getCurrencyCode()
        );

        $signaturePattern = Viabill_Payment_Helper_Signature::REFUND_ORDER_SIGNATURE_PATTERN;
        $fields['signature'] = Mage::helper('viabill/signature')->getSignature($fields, $signaturePattern);
        return $fields;
    }
}
