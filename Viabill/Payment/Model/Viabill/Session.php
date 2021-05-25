<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Model_Viabill_Session
 */
class Viabill_Payment_Model_Viabill_Session extends Mage_Core_Model_Session_Abstract
{
    /**
     * Viabill_Payment_Model_Viabill_Session constructor.
     */
    public function __construct()
    {
        $this->init('viabill');
    }
}
