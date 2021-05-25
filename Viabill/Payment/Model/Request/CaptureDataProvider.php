<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Model_Request_CaptureDataProvider
 */
class Viabill_Payment_Model_Request_CaptureDataProvider
{
    /**
     * @param array $commandSubject
     *
     * @return array
     */
    public function getFields(&$commandSubject = array())
    {
        $order = $commandSubject['order'];
        $additionaldata = json_decode($order->getPayment()->getAdditionalData(), true);
        if (empty($additionaldata['transaction'])) {
            Mage::throwException(
                Mage::helper('viabill')->__("This order hasn't been authorized so it can not be captured.")
            );
        }

        $helper = Mage::helper('viabill');
        $amount = 0 - $commandSubject['amount'];
        $fields = array(
            'id' => $additionaldata['transaction'],
            'apikey' => $helper->getViabillKey(),
            'amount' => (string)$amount,
            'currency' => $order->getOrderCurrency()->getCurrencyCode()
        );

        $signaturePattern = Viabill_Payment_Helper_Signature::CAPTURE_ORDER_SIGNATURE_PATTERN;
        $fields['signature'] = Mage::helper('viabill/signature')->getSignature($fields, $signaturePattern);
        return $fields;
    }
}
