<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Block_Form
 */
class Viabill_Payment_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $this->setTemplate('viabill/payment/form.phtml');
        parent::_construct();
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return Mage::getStoreConfig('payment/viabill_config_form/description');
    }
}
