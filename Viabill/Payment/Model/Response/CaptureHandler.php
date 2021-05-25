<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Model_Response_CaptureHandler
 */
class Viabill_Payment_Model_Response_CaptureHandler
{
    /**
     * @param $commandSubject
     * @param $response
     */
    public function handle($commandSubject, $response)
    {
        $order = $commandSubject['order'];
        $order->addStatusHistoryComment(
            Mage::helper('viabill')->__('Captured order on ViaBill.')
        );
        $order->save();
    }
}
