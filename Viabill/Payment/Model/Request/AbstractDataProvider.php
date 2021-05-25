<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Model_Request_AbstractDataProvider
 */
class Viabill_Payment_Model_Request_AbstractDataProvider
{
    /**
     * @param $commandSubject
     *
     * @return mixed
     * @throws Viabill_Payment_Model_Exception_ViabillException
     */
    protected function getKey($commandSubject)
    {
        if ($this->isValid($commandSubject)) {
            return $commandSubject['requestData']['key'];
        } else {
            $helper = Mage::helper('viabill');
            $errorMessage = !empty($commandSubject['errorMessage']) ?
                $commandSubject['errorMessage'] : $helper->__('Cannot get merchant\'s key. Please check credentials');
            $key = $helper->getViabillKey();
            if (empty($key)) {
                throw new Viabill_Payment_Model_Exception_ViabillException(
                    $errorMessage,
                    Viabill_Payment_Model_Exception_ViabillException::DEFAULT_CODE,
                    null
                );
            }

            return $key;
        }
    }

    /**
     * @param $commandSubject
     *
     * @return null
     */
    protected function getSecret($commandSubject)
    {
        return $this->isValid($commandSubject)
            ? $commandSubject['requestData']['secret']
            : null;
    }

    /**
     * @param $commandSubject
     *
     * @return bool
     */
    protected function isValid($commandSubject)
    {
        return (array_key_exists('requestData', $commandSubject)
            && is_array($commandSubject['requestData'])
            && array_key_exists('key', $commandSubject['requestData'])
            && array_key_exists('secret', $commandSubject['requestData'])
        );
    }
}
