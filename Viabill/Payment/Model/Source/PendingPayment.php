<?php

/**
 * Order Statuses source model
 */
class Viabill_Payment_Model_Source_PendingPayment extends Mage_Adminhtml_Model_System_Config_Source_Order_Status
{
    protected $_stateStatuses = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
}
