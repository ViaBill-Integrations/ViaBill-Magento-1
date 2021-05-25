<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Model_Http_Client
 */
class Viabill_Payment_Model_Http_Client
{
    /**
     * @var string
     */
    protected $_endpointUrl;
    /**
     * @var array
     */
    protected $_fields;
    /**
     * @var string
     */
    protected $_method;
    /**
     * @var array
     */
    protected $_headers;

    /**
     * @param string|array $endpointUrl
     * @param array        $fields
     * @param $method
     * @param array        $headers
     *
     * @return mixed
     * @throws Exception
     */
    public function placeRequest($endpointUrl, $fields, $method = null, $headers = array())
    {
        $this->_init($endpointUrl, $fields, $method, $headers);
        $http = new Varien_Http_Adapter_Curl();

        try {
            $http->setConfig(array('timeout' => 30));
            $http->write(
                $this->_getMethod(),
                $this->_getUrl(),
                '1.1',
                $this->_getHeaders(),
                $this->_getRequestBody()
            );
            $responseString = $http->read();
            if (stripos($responseString, "Transfer-Encoding: chunked\r\n") !== false) {
                $responseString = str_ireplace("Transfer-Encoding: chunked\r\n", '', $responseString);
            }

            $response = Zend_Http_Response::fromString($responseString);
            $http->close();
            return $response;
        } catch (Exception $e) {
            Mage::getModel('viabill/logger')->critical($e->getMessage());
            $http->close();
            throw $e;
        }
    }

    /**
     * @param $endpointUrl
     * @param $fields
     * @param $method
     * @param array       $headers
     */
    protected function _init($endpointUrl, $fields, $method = Zend_Http_Client::POST, $headers = null)
    {
        $this->_endpointUrl = $endpointUrl;
        $this->_fields = $fields;
        $this->_method = $method;
        $this->_headers = empty($headers) ? array() : $headers;
    }

    /**
     * @return string
     */
    protected function _getUrl()
    {
        $url = Mage::helper('viabill/url')->getApiUrl($this->_endpointUrl);
        if ($this->_method === Zend_Http_Client::GET) {
            $url .= "?" . http_build_query($this->_fields, '', '&');
        }

        return $url;
    }

    /**
     * @return array
     */
    protected function _getHeaders()
    {
        return array_merge(array('content-type: application/json'), $this->_headers);
    }

    /**
     * @return null|string
     */
    protected function _getRequestBody()
    {
        return json_encode($this->_fields);
    }

    /**
     * @return string
     */
    protected function _getMethod()
    {
        return empty($this->_method) ? Zend_Http_Client::POST : $this->_method;
    }
}
