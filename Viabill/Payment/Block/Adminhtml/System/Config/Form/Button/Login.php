<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Block_Adminhtml_System_Config_Form_Button_Login
 */
class Viabill_Payment_Block_Adminhtml_System_Config_Form_Button_Login extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /*
     * Set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('viabill/payment/login.phtml');
    }

    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    /**
     * Return ajax url for button
     *
     * @return string
     */
    public function getAjaxUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/index/loginviabilluser');
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
                'id'        => 'viabill_payment_login_user_button',
                'label'     => $this->helper('adminhtml')->__('Login ViaBill user'),
                'onclick'   => 'javascript:viabillLogin(); return false;'
                )
            );

        return $button->toHtml();
    }
}