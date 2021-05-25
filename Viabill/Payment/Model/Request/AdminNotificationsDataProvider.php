<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Model_Request_AdminNotificationsDataProvider
 */
class Viabill_Payment_Model_Request_AdminNotificationsDataProvider extends Viabill_Payment_Model_Request_AbstractDataProvider
{
    /**
     * @param array $commandSubject
     *
     * @return array
     * @throws Viabill_Payment_Model_Exception_ViabillException
     */
    public function getFields($commandSubject = array())
    {
        $fields = array(
            'key' => $this->getKey($commandSubject)
        );
        $signaturePattern = Viabill_Payment_Helper_Signature::DEFAULT_SIGNATURE_PATTERN;
        $fields['signature'] = Mage::helper('viabill/signature')
            ->getSignature($fields, $signaturePattern, $this->getSecret($commandSubject));
        return $fields;
    }
}
