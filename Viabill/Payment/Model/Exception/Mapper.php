<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Model_Exception_Mapper
 */
class Viabill_Payment_Model_Exception_Mapper
{
    /**
     * @var array
     */
    protected $_messages;

    /**
     * @param string $commandCode
     * @param int    $httpResponseCode
     *
     * @return null
     */
    public function getMessage($commandCode, $httpResponseCode)
    {
        if (empty($this->_messages)) {
            $this->loadDefaultMessages();
        }

        return isset($this->_messages[$commandCode][$httpResponseCode])
            ? $this->_messages[$commandCode][$httpResponseCode]
            : null;
    }

    /**
     * Load default error messages.
     */
    protected function loadDefaultMessages()
    {
        $this->_messages = array(
            Viabill_Payment_Model_Gateway::COMMAND_CAPTURE => array(
                400 => Mage::helper('viabill')
                    ->__('Error when trying to capture the order - please contact ViaBill for more information.'),
                403 => Mage::helper('viabill')
                    ->__('Capture is not longer possible for this transaction - please contact ViaBill for more information.'), // @codingStandardsIgnoreLine
                409 => Mage::helper('viabill')
                    ->__('You are not allowed to make several capture attempts in a very short time - please try again in 15 minutes or contact ViaBill for more info.'), // @codingStandardsIgnoreLine
                500 => Mage::helper('viabill')
                    ->__('Error when trying to capture the transaction - please try again in 15 minutes.')
            ),
            Viabill_Payment_Model_Gateway::COMMAND_REFUND => array(
                400 => Mage::helper('viabill')
                    ->__('Error when trying to refund the transaction - please contact ViaBill for more information.'),
                403 => Mage::helper('viabill')
                    ->__('Refund is not possible at the moment - please contact ViaBill for more information.'),
                500 => Mage::helper('viabill')
                    ->__('Error when trying to refund the transaction - please try again in 15 minutes.')
            ),
            Viabill_Payment_Model_Gateway::COMMAND_CANCEL => array(
                400 => Mage::helper('viabill')
                    ->__('Error when trying to cancel the transaction - please contact ViaBill for more information.'),
                500 => Mage::helper('viabill')
                    ->__('It\'s not possible to cancel the transaction - please try again in 15 minutes.')
            ),
            Viabill_Payment_Model_Gateway::COMMAND_RENEW => array(
                400 => Mage::helper('viabill')
                    ->__('It\'s not possible to renew the order at the moment - please contact ViaBill for more information.'), // @codingStandardsIgnoreLine
                403 => Mage::helper('viabill')
                    ->__('Renew is no longer possible for this transaction.'),
                500 => Mage::helper('viabill')
                    ->__('Error when trying to renew the order - please try again in 15 minutes.')
            )
        );
    }
}
