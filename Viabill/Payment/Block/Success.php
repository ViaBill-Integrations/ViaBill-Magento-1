<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Block_Success
 */
class Viabill_Payment_Block_Success extends Mage_Core_Block_Template
{

    /**
     * @return mixed
     */
    public function getViabill()
    {
        if ($this->getData('viabillmodel') == null) {
            $this->setViabillmodel(Mage::getModel('viabill/viabill'));
        }
        return $this->getData('viabillmodel');
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('viabill/payment/success.phtml');
    }
}

