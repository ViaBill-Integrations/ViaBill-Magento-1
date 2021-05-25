<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Helper_Checkout
 * Checkout workflow helper
 */
class Viabill_Payment_Helper_Checkout extends Mage_Core_Helper_Abstract
{
    /**
     * Cancel last placed order with specified comment message
     *
     * @param string $comment Comment appended to order history
     *
     * @return bool True if order cancelled, false otherwise
     * @throws Mage_Core_Exception
     */
    public function cancelCurrentOrder($comment)
    {
        $order = $this->_getCheckoutSession()->getLastRealOrder();
        if ($order->getId() && $order->getState() != Mage_Sales_Model_Order::STATE_CANCELED) {
            $order->registerCancellation($comment)->save();
            return true;
        }

        return false;
    }

    /**
     * Restore last active quote based on checkout session
     *
     * @return bool True if quote restored successfully, false otherwise
     */
    public function restoreQuote()
    {
        $order = $this->_getCheckoutSession()->getLastRealOrder();
        if ($order->getId()) {
            $quote = Mage::getModel('sales/quote')->load($order->getQuoteId());
            if ($quote->getId()) {
                $quote->setIsActive(1)
                    ->setReservedOrderId(null)
                    ->save();
                $this->_getCheckoutSession()
                    ->replaceQuote($quote)
                    ->unsLastRealOrderId();
                return true;
            }
        }

        return false;
    }

    /**
     * Return checkout session instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }
}
