<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Helper_Data
 */
class Viabill_Payment_Helper_Data extends Mage_Payment_Helper_Data
{
    const PREFIX_HASH_LENGTH = 7;

    /**
     * @return boolean
     */
    public function isExtensionEnabled()
    {
        return Mage::getStoreConfigFlag('payment/viabill_config_form/active');
    }

    /**
     * @return boolean
     */
    public function isSandbox()
    {
        return Mage::getStoreConfigFlag('payment/viabill_config_form/sandbox');
    }

    /**
     * @return boolean
     */
    public function isDebug()
    {
        return Mage::getStoreConfigFlag('payment/viabill_config_form/debug');
    }

    /**
     * @return string
     */
    public function getViabillKey()
    {
        return Mage::helper('core')->decrypt(Mage::getStoreConfig('payment/viabill_config_form/key'));
    }

    /**
     * @param $orderId
     *
     * @return string
     */
    public function generateViabillTransactionId($orderId)
    {
        $key = $this->getViabillKey();
        $storeId = Mage::app()->getStore()->getStoreId();
        $prefix = substr(sha1($key . $orderId . $storeId), self::PREFIX_HASH_LENGTH);
        return uniqid($prefix, false);
    }

    /**
     * @return bool
     */
    public function isViaBillUser()
    {
        return Mage::getStoreConfigFlag('payment/viabill_config_form/key');
    }
}
