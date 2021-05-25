<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Model_Request_RenewDataProvider
 */
class Viabill_Payment_Model_Response_RenewHandler
{
    /**
     * @param $commandSubject
     * @param $response
     */
    public function handle($commandSubject, $response)
    {
        $order = $commandSubject['order'];
        $order->addStatusHistoryComment(
            Mage::helper('viabill')->__('Renewed order on ViaBill.')
        );
        $order->save();
    }
}
