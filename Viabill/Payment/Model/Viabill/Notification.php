<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Model_Viabill_Notification
 */
class Viabill_Payment_Model_Viabill_Notification
{
    public function registerNotifications()
    {
        $helper = Mage::helper('viabill');
        $gateway = Mage::getModel('viabill/gateway');
        try{
            $gateway->registerAdminNotifications();
        } catch (Viabill_Payment_Model_Exception_ViabillException $e) {
            Mage::getSingleton('core/session')->addError(
                $helper->__($e->getMessage())
            );
        } catch (Exception $e) {
            Mage::getModel('viabill/logger')->critical($e->getMessage());
            Mage::getSingleton('core/session')->addError(
                $helper->__('An error occurred during getting notifications from ViaBill.')
            );
        }
    }
}
