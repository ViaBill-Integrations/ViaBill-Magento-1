<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Block_Adminhtml_Page_Notices
 */
class Viabill_Payment_Block_Adminhtml_Page_Notices extends Mage_Adminhtml_Block_Template
{
    /**
     * Check if ViaBill test transaction store notice should be displayed.
     *
     * @return boolean
     */
    public function displayTestTransactionNotice()
    {
        return Mage::getStoreConfig('payment/viabill_config_form/test_transaction');
    }

}
