<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Model_Exception_Factory
 */
class Viabill_Payment_Model_Exception_Factory
{
    /**
     * Generate exception depending on response.
     *
     * @param Zend_Http_Response $response
     * @param $commandCode
     *
     * @return Viabill_Payment_Model_Exception_ViabillException
     */
    public function create(Zend_Http_Response $response, $commandCode)
    {
        $responseErrorMessage = $this->getResponseErrorMessage($response);
        $errorCode = $response->getStatus();
        /**
 * @var Viabill_Payment_Model_Exception_Mapper $messagesMapper 
*/
        $messagesMapper = Mage::getModel('viabill/exception_mapper');
        $errorMessage = $messagesMapper->getMessage($commandCode, $errorCode);
        if (!$errorMessage) {
            $errorCode = Viabill_Payment_Model_Exception_ViabillException::DEFAULT_CODE;
            $errorMessage = !empty($responseErrorMessage)
                ? $responseErrorMessage
                : Viabill_Payment_Model_Exception_ViabillException::DEFAULT_MESSAGE;
        }

        // We need to log initial error message because it has more details
        Mage::logException(
            new Exception(
                !empty($responseErrorMessage)
                ? $responseErrorMessage
                : $errorMessage
            )
        );

        return new Viabill_Payment_Model_Exception_ViabillException(
            Mage::helper('viabill')->__($errorMessage),
            $errorCode,
            null
        );
    }

    /**
     * @param Zend_Http_Response $response
     *
     * @return null|string
     */
    protected function getResponseErrorMessage(Zend_Http_Response $response)
    {
        try {
            $responseBody = json_decode($response->getBody(), true);
            if (is_array($responseBody) && array_key_exists('errors', $responseBody)) {
                foreach ($responseBody['errors'] as $message) {
                    if (!empty($message['field'])) {
                        $errorMessages[] = "\"{$message['field']}\" {$message['error']}";
                    } else {
                        $errorMessages[] = $message['error'];
                    }
                }

                $errorMessage = implode(', ', $errorMessages);
            } elseif (is_string($responseBody)) {
                $errorMessage = $responseBody;
            }
        } catch (Exception $e) {
            $logger = Mage::getModel('viabill/logger');
            $logger->critical($e->getMessage());
            $logger->debug($response->getBody());
            Mage::logException($e);
        }

        return !empty($errorMessage) ? $errorMessage : null;
    }
}
