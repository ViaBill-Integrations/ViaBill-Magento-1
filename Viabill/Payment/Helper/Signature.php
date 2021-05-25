<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Helper_Signature
 */
class Viabill_Payment_Helper_Signature extends Mage_Core_Helper_Data
{
    const MYVIABILL_SIGNATURE_PATTERN = 'key';
    const NEW_ORDER_SIGNATURE_PATTERN = 'apikey#amount#currency#transaction#order_number#success_url#cancel_url';
    const CAPTURE_ORDER_SIGNATURE_PATTERN = 'id#apikey#amount#currency';
    const REFUND_ORDER_SIGNATURE_PATTERN = 'id#apikey#amount#currency';
    const CALLBACK_REQUEST_SIGNATURE_PATTERN = 'transaction#orderNumber#amount#currency#status#time';
    const DEFAULT_TRANSACTION_SIGNATURE_PATTERN = 'id#apikey';
    const DEFAULT_SIGNATURE_PATTERN = 'key';

    /**
     * @param $fields
     * @param $signaturePattern
     * @param null             $secret
     * @return string
     */
    public function getSignature($fields, $signaturePattern, $secret = null)
    {
        $delimiter = '#';
        $signature = explode($delimiter, $signaturePattern);
        if ($secret === null) {
            $secret = Mage::helper('core')->decrypt(Mage::getStoreConfig('payment/viabill_config_form/secret'));
        }

        foreach ($signature as $key => $field) {
            if (isset($fields[$field])) {
                $signature[$key] = $fields[$field];
            }
        }

        $signature = implode($delimiter, $signature);

        return hash('sha256', $signature . $delimiter . $secret);
    }
}
