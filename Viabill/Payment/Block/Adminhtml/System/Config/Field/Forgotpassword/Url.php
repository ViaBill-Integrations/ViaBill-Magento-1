<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Block_Adminhtml_System_Config_Field_Forgotpassword_Url
 */
class Viabill_Payment_Block_Adminhtml_System_Config_Field_Forgotpassword_Url extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $url = Mage::helper('viabill/url')->getForgotPasswordUrl();
            $html = '<a target="_blank" href="' . $url . '">'
            . Mage::helper('viabill')->__('Forgot password?') . '</a>';

        return $html;
    }
}