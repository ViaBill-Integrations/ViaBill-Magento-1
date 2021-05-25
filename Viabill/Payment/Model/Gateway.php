<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Model_Gateway
 */
class Viabill_Payment_Model_Gateway
{
    const COMMAND_CAPTURE = 'capture';
    const COMMAND_REFUND = 'refund';
    const COMMAND_CANCEL = 'cancel';
    const COMMAND_RENEW = 'renew';
    const COMMAND_REGISTER = 'register';
    const COMMAND_LOGIN = 'login';
    const COMMAND_COUNTRIES = 'countries';
    const COMMAND_PRICETAG_CONFIG = 'pricetag_config';
    const STATUS_CODE_204 = 204;

    /**
     * @param $endpointUrl
     * @param string      $commandCode
     * @param null        $dataProvider
     * @param array       $commandSubject
     * @param null        $responseHandler
     *
     * @return array
     * @throws Exception
     * @throws Viabill_Payment_Model_Exception_ViabillException
     */
    protected function execute(
        $endpointUrl,
        $commandCode = '',
        $dataProvider = null,
        $commandSubject = array(),
        $responseHandler = null
    ) {
        /**
 * @var Viabill_Payment_Model_Http_Client $http 
*/
        $http = Mage::getModel('viabill/http_client');
        $logger = Mage::getModel('viabill/logger');
        $response = $http->placeRequest(
            $endpointUrl,
            $this->getRequestBody($dataProvider, $commandSubject),
            isset($commandSubject['method']) ? $commandSubject['method'] : null,
            isset($commandSubject['headers']) ? $commandSubject['headers'] : null
        );
        if (!$response->isSuccessful()) {
            /**
 * @var Viabill_Payment_Model_Exception_ViabillException $viabillException 
*/
            $viabillException = Mage::getModel('viabill/exception_factory')->create($response, $commandCode);
            $logger->critical($viabillException->getMessage());
            throw $viabillException;
        }

        $responseBody = $this->getResponseBody($response);
        $logger->debug('Request data: ' . json_encode($commandSubject));
        $logger->debug('Response body: ' . json_encode($responseBody));

        /**
 * Handling response after validation is success 
*/
        if (null !== $responseHandler) {
            $responseHandler->handle($commandSubject, $responseBody);
        }

        return $responseBody;
    }

    /**
     * @param $dataProvider
     * @param $commandSubject
     *
     * @return array
     */
    protected function getRequestBody($dataProvider = null, $commandSubject = array())
    {
        if ($dataProvider === null) {
            return array();
        }

        return $dataProvider->getFields($commandSubject);
    }

    /**
     * @param Zend_Http_Response $response
     *
     * @return array
     */
    protected function getResponseBody(Zend_Http_Response $response)
    {
        if ($response->getStatus() !== self::STATUS_CODE_204) {
            return json_decode($response->getBody(), true);
        }

        return array();
    }

    /**
     * @param $order
     *
     * @return array
     * @throws Exception
     * @throws Viabill_Payment_Model_Exception_ViabillException
     */
    public function renewOrder($order)
    {
        $dataProvider = Mage::getModel('viabill/request_renewDataProvider');
        $responseHandler = Mage::getModel('viabill/response_renewHandler');
        return $this->execute(
            Viabill_Payment_Helper_Url::ENDPOINT_URL_RENEW,
            self::COMMAND_RENEW,
            $dataProvider,
            array('order' => $order),
            $responseHandler
        );
    }

    /**
     * @param $order
     * @param $inamount
     *
     * @return array
     * @throws Exception
     * @throws Viabill_Payment_Model_Exception_ViabillException
     */
    public function captureOrder($order, $inamount)
    {
        $dataProvider = Mage::getModel('viabill/request_captureDataProvider');
        $responseHandler = Mage::getModel('viabill/response_captureHandler');
        return $this->execute(
            Viabill_Payment_Helper_Url::ENDPOINT_URL_CAPTURE,
            self::COMMAND_CAPTURE,
            $dataProvider,
            array('order' => $order, 'amount' => $inamount),
            $responseHandler
        );
    }

    /**
     * @param $order
     *
     * @return array
     * @throws Exception
     * @throws Viabill_Payment_Model_Exception_ViabillException
     */
    public function refundOrder($order, $inamount)
    {
        $dataProvider = Mage::getModel('viabill/request_refundDataProvider');
        $responseHandler = Mage::getModel('viabill/response_refundHandler');

        return $this->execute(
            Viabill_Payment_Helper_Url::ENDPOINT_URL_REFUND,
            self::COMMAND_REFUND,
            $dataProvider,
            array('order' => $order, 'amount' => $inamount),
            $responseHandler
        );
    }

    /**
     * @param $order
     *
     * @return array
     * @throws Exception
     * @throws Viabill_Payment_Model_Exception_ViabillException
     */
    public function cancelOrder($order)
    {
        $dataProvider = Mage::getModel('viabill/request_cancelDataProvider');
        return $this->execute(
            Viabill_Payment_Helper_Url::ENDPOINT_URL_RENEW,
            self::COMMAND_CANCEL,
            $dataProvider,
            array('order' => $order)
        );
    }

    /**
     * @return array
     * @throws Exception
     * @throws Viabill_Payment_Model_Exception_ViabillException
     */
    public function registerAdminNotifications($requestData = null)
    {
        $dataProvider = Mage::getModel('viabill/request_adminNotificationsDataProvider');
        $responseHandler = Mage::getModel('viabill/response_adminNotificationsHandler');
        $commandSubject = array('method' => Zend_Http_Client::GET);
        if ($requestData !== null) {
            $commandSubject['requestData'] = $requestData;
        }
        $commandSubject['errorMessage'] =
            Mage::helper('viabill')->__('Cannot get notifications from ViaBill. Please check your credentials.');

        return $this->execute(
            Viabill_Payment_Helper_Url::ENDPOINT_ADMIN_NOTIFICATIONS,
            null,
            $dataProvider,
            $commandSubject,
            $responseHandler
        );
    }
    /**
     * @return array
     * @throws Exception
     * @throws Viabill_Payment_Model_Exception_ViabillException
     */
    public function getMyViaBillUrl()
    {
        $dataProvider = Mage::getModel('viabill/request_myViaBillDataProvider');
        return $this->execute(
            Viabill_Payment_Helper_Url::ENDPOINT_MY_VIABILL,
            null,
            $dataProvider,
            array('method' => Zend_Http_Client::GET)
        );
    }

    /**
     * @return array
     * @throws Exception
     * @throws Viabill_Payment_Model_Exception_ViabillException
     */
    public function registerUser()
    {
        $dataProvider = Mage::getModel('viabill/request_registerDataProvider');
        return $this->execute(
            Viabill_Payment_Helper_Url::ENDPOINT_REGISTER_USER,
            self::COMMAND_REGISTER,
            $dataProvider,
            array('params' => Mage::app()->getRequest()->getParams())
        );
    }

    /**
     * @return array
     * @throws Exception
     * @throws Viabill_Payment_Model_Exception_ViabillException
     */
    public function loginUser()
    {
        $dataProvider = Mage::getModel('viabill/request_loginDataProvider');
        return $this->execute(
            Viabill_Payment_Helper_Url::ENDPOINT_LOGIN_USER,
            self::COMMAND_LOGIN,
            $dataProvider,
            array('params' => Mage::app()->getRequest()->getParams())
        );
    }

    /**
     * @return array
     * @throws Exception
     * @throws Viabill_Payment_Model_Exception_ViabillException
     */
    public function getCountries()
    {
        return $this->execute(
            Viabill_Payment_Helper_Url::ENDPOINT_SUPPORTED_COUNTRIES . Mage::app()
                ->getLocale()
                ->getLocaleCode(),
            self::COMMAND_COUNTRIES,
            null,
            array('method' => Zend_Http_Client::GET)
        );
    }

    /**
     * @return array
     * @throws Exception
     * @throws Viabill_Payment_Model_Exception_ViabillException
     */
    public function setPricetagConfig($data)
    {
        $dataProvider = Mage::getModel('viabill/request_pricetagConfigDataProvider');
        return $this->execute(
            Viabill_Payment_Helper_Url::ENDPOINT_PRICETAG_CONFIG,
            self::COMMAND_PRICETAG_CONFIG,
            $dataProvider,
            array('requestData' => $data)
        );
    }
}
