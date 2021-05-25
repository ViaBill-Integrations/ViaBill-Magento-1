<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */

/**
 * Class Viabill_Payment_Model_Config
 */
class Viabill_Payment_Model_Config extends Mage_Payment_Model_Method_Abstract
{
    /**
     * @var string
     */
    protected $_code = Viabill_Payment_Model_Viabill::VIABILL_UNIQUE_IDENTITY;

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
        $path = 'payment/' . Viabill_Payment_Model_Viabill::VIABILL_UNIQUE_IDENTITY . '_config_form/' . $field;
        $configValue = Mage::getStoreConfig($path, $storeId);
        if (empty($configValue)) {
            return parent::getConfigData($field, $storeId);
        }

        return $configValue;
    }
}
