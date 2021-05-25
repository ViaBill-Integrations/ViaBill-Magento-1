<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Model_Request_CancelDataProvider
 */
class Viabill_Payment_Model_Request_CancelDataProvider
{
    /**
     * @param array $commandSubject
     *
     * @return array
     */
    public function getFields(&$commandSubject = array())
    {
        $order = $commandSubject['order'];
        $authTransactionId = $order->getPayment()->getAuthorizationTransaction()->getTxnId();
        if (empty($authTransactionId)) {
            Mage::throwException(
                Mage::helper('viabill')->__("This order hasn't been authorized so it can not be cancelled on ViaBill.")
            );
        }

        $helper = Mage::helper('viabill');
        $fields = array(
            'id' => $authTransactionId,
            'apikey' => $helper->getViabillKey()
        );
        $signaturePattern = Viabill_Payment_Helper_Signature::DEFAULT_TRANSACTION_SIGNATURE_PATTERN;
        $fields['signature'] = Mage::helper('viabill/signature')->getSignature($fields, $signaturePattern);
        return $fields;
    }
}
