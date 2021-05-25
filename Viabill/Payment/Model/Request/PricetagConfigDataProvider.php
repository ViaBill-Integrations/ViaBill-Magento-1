<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Model_Request_PricetagConfigDataProvider
 */
class Viabill_Payment_Model_Request_PricetagConfigDataProvider extends Viabill_Payment_Model_Request_AbstractDataProvider
{
    /**
     * @param array $commandSubject
     *
     * @return array
     */
    public function getFields(&$commandSubject = array())
    {
        $fields = array(
            'key' => $this->getKey($commandSubject),
            'useObserver' => 'true'
        );

        $signaturePattern = Viabill_Payment_Helper_Signature::DEFAULT_SIGNATURE_PATTERN;
        $fields['signature'] = Mage::helper('viabill/signature')
            ->getSignature($fields, $signaturePattern, $this->getSecret($commandSubject));
        return $fields;
    }
}
