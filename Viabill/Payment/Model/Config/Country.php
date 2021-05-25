<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Model_Config_Country
 */
class Viabill_Payment_Model_Config_Country extends Varien_Object
{
    /**
     * @var array
     */
    protected $_options;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (! $this->_options) {
            /**
 * @var Viabill_Payment_Helper_Data $helper 
*/
            $helper = Mage::helper('viabill');
            $response = array();
            /**
 * @var Viabill_Payment_Model_Gateway $gateway 
*/
            $gateway = Mage::getModel('viabill/gateway');

            try {
                $response = $gateway->getCountries();
                if (empty($response)) {
                    Mage::getSingleton('adminhtml/session')
                        ->addError('Can\'t fetch ViaBill supported countries');
                    return $this->_options;
                }

                $this->_options[] = array('value' => '', 'label' => Mage::helper('adminhtml')
                    ->__('-- Please Select --'));

                foreach ($response as $item) {
                    $this->_options[] = array('value' => $item['code'], 'label' => $item['name']);
                }
            } catch (Viabill_Payment_Model_Exception_ViabillException $e) {
                $response['error'] = $e->getMessage();
            } catch (Exception $e) {
                $response['error'] = $helper->__('An error occurred during supported countries fetching. Please try again later.');
                Mage::getModel('viabill/logger')->critical($e->getMessage());
            }
        }

        return $this->_options;
    }
}