<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Model_Request_AdminNotificationsDataProvider
 */
class Viabill_Payment_Model_Request_MyViaBillDataProvider
{
    /**
     * @return array
     */
    public function getFields()
    {
        $helper = Mage::helper('viabill');
        $fields = array(
            'key' => $helper->getViabillKey()
        );
        $signaturePattern = Viabill_Payment_Helper_Signature::DEFAULT_SIGNATURE_PATTERN;
        $fields['signature'] = Mage::helper('viabill/signature')->getSignature($fields, $signaturePattern);
        return $fields;
    }
}
