<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Block_Adminhtml_System_Config_Field_Myviabill_Url
 */
class Viabill_Payment_Block_Adminhtml_System_Config_Field_Myviabill_Url extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $helper = Mage::helper('viabill');
        $url = Mage::helper('adminhtml')->getUrl('adminhtml/index/myviabill');

        $html = $helper->__('Log in to')
            . ' <a target="_blank" href="' . $url . '">'
            . $helper->__('MyViabill') . '</a>';

        return $html;
    }
}