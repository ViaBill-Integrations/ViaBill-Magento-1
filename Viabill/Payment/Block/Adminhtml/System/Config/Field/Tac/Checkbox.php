<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Block_Adminhtml_System_Config_Field_Tac_Checkbox
 */
class Viabill_Payment_Block_Adminhtml_System_Config_Field_Tac_Checkbox extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    const TERMS_AND_CONDITIONS_DEFAULT_URL = 'http://www.viabill.com/trade-terms/';

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('viabill/payment/tac.phtml');
    }

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $isViaBillUser = Mage::helper('viabill')->isViaBillUser();
        if ($isViaBillUser) {
            return '';
        }

        return $this->_toHtml();
    }

    /**
     * @return string
     */
    public function getTermsAndConditionsUrl()
    {
        return self::TERMS_AND_CONDITIONS_DEFAULT_URL;
    }
}