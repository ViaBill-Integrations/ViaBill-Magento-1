<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Adminhtml_RenewController
 */
class Viabill_Payment_Adminhtml_RenewController extends Mage_Adminhtml_Controller_Action
{
    public function reneworderAction()
    {
        /**
 * @var Viabill_Payment_Helper_Data $helper 
*/
        $helper = Mage::helper('viabill');
        $requestParams = $this->getRequest()->getParams();
        Mage::getModel('viabill/logger')->debug(json_encode($requestParams));
        if (!array_key_exists('order_id', $requestParams)) {
            Mage::throwException(
                $helper->__('Incorrect order id. Please return to order page and try again.')
            );
        }

        $order = Mage::getModel('sales/order')->load($requestParams['order_id']);
        if (!$order) {
            Mage::throwException(
                $helper->__('The order wasn\'t found. Please return to order page and try again.')
            );
        }

        /**
 * @var Viabill_Payment_Model_Gateway $gateway 
*/
        $gateway = Mage::getModel('viabill/gateway');
        try{
            $gateway->renewOrder($order);
            Mage::getSingleton('core/session')->addSuccess(
                $helper->__('The order has been successfully renewed.')
            );
        } catch (Viabill_Payment_Model_Exception_ViabillException $e) {
            Mage::getSingleton('core/session')->addError(
                $helper->__($e->getMessage())
            );
        } catch (Exception $e) {
            Mage::getModel('viabill/logger')->critical($e->getMessage());
            Mage::getSingleton('core/session')->addError(
                $helper->__('An error occurred during order renewal. Please try again later.')
            );
        }
        $this->_redirectReferer();
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('viabill/order/renew');
    }
}