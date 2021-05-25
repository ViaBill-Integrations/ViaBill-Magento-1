<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Adminhtml_IndexController
 */
class Viabill_Payment_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function registerviabilluserAction()
    {
        /**
 * @var Viabill_Payment_Helper_Data $helper 
*/
        $helper = Mage::helper('viabill');
        $response = array();
        /**
 * @var Viabill_Payment_Model_Gateway $gateway 
*/
        $gateway = Mage::getModel('viabill/gateway');
        try{
            $response = $gateway->registerUser();
            if (empty($response) || isset($response['errors'])) {
                Mage::getSingleton('core/session')->addError(
                    $helper->__('Registration failed')
                );
            }

            if (empty($response['error'])) {
                $gateway->setPricetagConfig($response);
                Mage::getSingleton('core/session')->addSuccess(
                    $helper->__('Registration successful')
                );
            }
        } catch (Viabill_Payment_Model_Exception_ViabillException $e) {
            Mage::getSingleton('core/session')->addError(
                $e->getMessage()
            );
        } catch (Exception $e) {
            Mage::getModel('viabill/logger')->critical($e->getMessage());
            Mage::getSingleton('core/session')->addError(
                $helper->__('An error occurred during user registration. Please try again later.')
            );
        }

        $this->getResponse()->setBody(json_encode($response));
    }

    public function loginviabilluserAction()
    {
        /**
 * @var Viabill_Payment_Helper_Data $helper 
*/
        $helper = Mage::helper('viabill');
        $response = array();
        /**
 * @var Viabill_Payment_Model_Gateway $gateway 
*/
        $gateway = Mage::getModel('viabill/gateway');

        try{
            $response = $gateway->loginUser();
            if (empty($response) || isset($response['errors'])) {
                Mage::getSingleton('core/session')->addError(
                    $helper->__('Login failed')
                );
            }

            if (empty($response['error'])) {
                $gateway->setPricetagConfig($response);
                Mage::getSingleton('core/session')->addSuccess(
                    $helper->__('ViaBill login successful')
                );

                $this->registerAdminNotifications($response);
            }
        } catch (Viabill_Payment_Model_Exception_ViabillException $e) {
            Mage::getSingleton('core/session')->addError(
                $e->getMessage()
            );
        } catch (Exception $e) {
            Mage::getModel('viabill/logger')->critical($e->getMessage());
            Mage::getSingleton('core/session')->addError(
                $helper->__('An error occurred during user login. Please try again later.')
            );
        }

        $this->getResponse()->setBody(json_encode($response));
    }

    public function myviabillAction()
    {
        $helper = Mage::helper('viabill');
        if (!$helper->isExtensionEnabled()) {
            Mage::getSingleton('core/session')->addError(
                $helper->__('MyViaBill module is not enabled. Please enable it and try again.')
            );
            $this->_redirectReferer();
            return;
        }

        $myViaBill = Mage::getModel('viabill/viabill_myViaBill');
        $url = $myViaBill->getMyViaBillUrl();

        if (!empty($url)) {
            $this->_redirectUrl($url);
        } else {
            $this->_redirectReferer();
        }
    }

    /**
     * @param $response
     */
    protected function registerAdminNotifications($response)
    {
        if (array_key_exists('key', $response)
            && array_key_exists('secret', $response)
        ) {
            $requestData = array(
                'key' => $response['key'],
                'secret' => $response['secret']
            );
            $notification = Mage::getModel('viabill/viabill_myViaBill');
            $notification->registerNotifications($requestData);
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin');
    }
}
