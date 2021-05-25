<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Model_Logger
 */
class Viabill_Payment_Model_Logger
{
    const FILENAME_DEBUG = '/var/log/viabill_debug.log';
    const FILENAME_CRITICAL = '/var/log/viabill_critical.log';

    /**
     * @param $msg
     */
    public function debug($msg)
    {
        if (Mage::helper('viabill')->isDebug()) {
            Mage::log($msg, Zend_Log::DEBUG, self::FILENAME_DEBUG, true);
        }
    }

    /**
     * @param $msg
     */
    public function critical($msg)
    {
        Mage::log($msg, Zend_Log::CRIT, self::FILENAME_CRITICAL, true);
    }
}
