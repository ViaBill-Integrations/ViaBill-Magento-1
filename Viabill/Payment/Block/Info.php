<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Block_Info
 */
class Viabill_Payment_Block_Info extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('viabill/payment/info.phtml');
    }

    /**
     * @return array|mixed
     */
    public function getPaymentInfo()
    {
        try {
            $arr = json_decode($this->getInfo()->getAdditionalData(), true);
        } catch (Exception $e) {
            $logger = Mage::getModel('viabill/logger');
            $logger->critical($e->getMessage());
            $logger->debug($this->getInfo()->getAdditionalData());
            $arr = array();
            $arr['errorMessage'] = "Identification information corrupt, unknown order ID";
        }

        return $arr;
    }
}
