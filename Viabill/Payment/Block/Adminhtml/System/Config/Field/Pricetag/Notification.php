<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Block_Adminhtml_System_Config_Field_Pricetag_Notification
 */
class Viabill_Payment_Block_Adminhtml_System_Config_Field_Pricetag_Notification
    extends Mage_Adminhtml_Block_System_Config_Form_Field
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
        $html = $helper->__('Acquiring with ViaBill will increase to 2% when deactivating PriceTag');
        $html .= '<input id="payment_viabill_price_tag_notification" name="groups[module_name][fields][titles][value]" value="" class=" input-text" type="hidden">';
        return $html;
    }
}