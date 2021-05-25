<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Model_Request_RegisterDataProvider
 */
class Viabill_Payment_Model_Request_RegisterDataProvider
{
    /**
     * @param array $commandSubject
     *
     * @return array
     */
    public function getFields(&$commandSubject = array())
    {
        $params = $commandSubject['params'];
        $additionalInfo = array();

        if (!empty($params['name'])) {
            $additionalInfo[] = $params['name'];
        }
        if (!empty($params['phone_number'])) {
            $additionalInfo[] = $params['phone_number'];
        }
        if (!empty($params['merchant_name'])) {
            $additionalInfo[] = $params['merchant_name'];
        }

        $fields = array(
            'email' => $params['email'],
            'country' => $params['country'],
            'url' => $params['shop_url'],
            'affiliate' => 'MAGENTO',
            'additionalInfo' => $additionalInfo
        );

        return $fields;
    }
}
