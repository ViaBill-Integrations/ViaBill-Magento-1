<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Model_Exception_ViabillException
 */
class Viabill_Payment_Model_Exception_ViabillException extends Exception
{
    const DEFAULT_CODE = 400;
    const DEFAULT_MESSAGE = 'Couldn\'t process this request. Please try again later or contact a store administrator.';
}
