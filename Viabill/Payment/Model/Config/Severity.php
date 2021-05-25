<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Model_Config_Severity
 */
class Viabill_Payment_Model_Config_Severity extends Varien_Object
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $helper = Mage::helper('adminhtml');
        return array(
            array('value' => '', 'label' => $helper->__('--Please Select--')),
            array('value' => Mage_AdminNotification_Model_Inbox::SEVERITY_CRITICAL, 'label' => $helper->__('Critical')),
            array('value' => Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR, 'label' => $helper->__('Major')),
            array('value' => Mage_AdminNotification_Model_Inbox::SEVERITY_MINOR, 'label' => $helper->__('Minor')),
            array('value' => Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE, 'label' => $helper->__('Notice'))
        );
    }
}