<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Model_Request_LoginDataProvider
 */
class Viabill_Payment_Model_Request_LoginDataProvider
{
    /**
     * @param array $commandSubject
     *
     * @return array
     */
    public function getFields(&$commandSubject = array())
    {
        $params = $commandSubject['params'];
        $fields = array(
            'email' => $params['email'],
            'password' => $params['password']
        );

        return $fields;
    }
}
