<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Helper_Url
 */
class Viabill_Payment_Helper_Url extends Mage_Core_Helper_Data
{
    const API_BASE_PRODUCTION_URL = 'https://secure.viabill.com';
    const ENDPOINT_URL_ORDER = '/api/checkout-authorize/addon/magento';
    const ENDPOINT_URL_RENEW = '/api/transaction/renew';
    const ENDPOINT_URL_CAPTURE = '/api/transaction/capture';
    const ENDPOINT_URL_REFUND = '/api/transaction/refund';
    const ENDPOINT_URL_CANCEL = '/api/transaction/cancel';
    const ENDPOINT_ADMIN_NOTIFICATIONS = '/api/addon/magento/notifications';
    const ENDPOINT_MY_VIABILL = '/api/addon/magento/myviabill';
    const ENDPOINT_REGISTER_USER = '/api/addon/magento/register';
    const ENDPOINT_LOGIN_USER = '/api/addon/magento/login';
    const ENDPOINT_SUPPORTED_COUNTRIES = '/api/addon/magento/countries/supported/';
    const ENDPOINT_PRICETAG_CONFIG = '/api/addon/magento/pricetag/config';
    const VIABILL_FORGOT_PASSWORD_URL = 'https://viabill.com/auth/forgot';

    /**
     * @return string
     */
    public function getApiBaseUrl()
    {
        return self::API_BASE_PRODUCTION_URL;
    }

    /**
     * @return string
     */
    public function getForgotPasswordUrl()
    {
        return self::VIABILL_FORGOT_PASSWORD_URL;
    }

    /**
     * Generating Url.
     *
     * @param string $endpointUrl
     * @param array  $urlParams
     *
     * @return string
     */
    public function getApiUrl($endpointUrl = '', $urlParams = array())
    {
        /**
 * Binding url parameters if they were specified 
*/
        foreach ($urlParams as $paramName => $paramValue) {
            $endpointUrl = str_replace(':' . $paramName, $paramValue, $endpointUrl);
        }

        return $this->getApiBaseUrl() . $endpointUrl;
    }
}
