<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Block_Adminhtml_System_Config_Form_Button_Register
 */
class Viabill_Payment_Block_Adminhtml_System_Config_Form_Button_Register extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /*
     * Set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('viabill/payment/register.phtml');
    }

    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
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
     * Return ajax url for button
     *
     * @return string
     */
    public function getAjaxUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/index/registerviabilluser');
    }

    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                'id'        => 'viabill_payment_register_new_user_button',
                'label'     => $this->helper('adminhtml')->__('Register new ViaBill user'),
                'onclick'   => 'javascript:viabillRegister(); return false;'
                )
            );

        return $button->toHtml();
    }
}