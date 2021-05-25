<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Model_Viabill_MyViaBill
 */
class Viabill_Payment_Model_Viabill_MyViaBill
{
    /**
     * @return mixed|string
     */
    public function getMyViaBillUrl()
    {
        $result = array();
        $helper = Mage::helper('viabill');
        $gateway = Mage::getModel('viabill/gateway');
        try{
            $result = $gateway->getMyViaBillUrl();
        } catch (Viabill_Payment_Model_Exception_ViabillException $e) {
            Mage::getSingleton('core/session')->addError(
                $helper->__($e->getMessage())
            );
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError(
                $helper->__('An error occurred during getting MyViaBill url.')
            );
            Mage::getModel('viabill/logger')->critical($e->getMessage());
        }

        return !empty($result['url']) ? $result['url'] : '';
    }

    /**
     * @param array|null $requestData
     */
    public function registerNotifications($requestData = null)
    {
        $helper = Mage::helper('viabill');
        $gateway = Mage::getModel('viabill/gateway');
        try{
            $gateway->registerAdminNotifications($requestData);
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
