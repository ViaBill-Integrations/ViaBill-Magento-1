<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Model_Observer
 */
class Viabill_Payment_Model_Observer
{
    /**
     * @param $event
     */
    public function adminhtmlWidgetContainerHtmlBefore($event)
    {
        if (!Mage::helper('viabill')->isExtensionEnabled()) {
            return;
        }

        $block = $event->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View
            && $block->getOrder()->getState() === Mage_Sales_Model_Order::STATE_PROCESSING
        ) {
            if (Mage::getSingleton('admin/session')->isAllowed('viabill/order/renew')) {
                $message = Mage::helper('viabill')->__(
                    'This will renew the order on ViaBill. Do you want to continue?'
                );
                $block->addButton(
                    'renew',
                    array(
                        'label'     => Mage::helper('viabill')->__('Renew Order'),
                        'onclick'   => "confirmSetLocation('{$message}', '{$block->getUrl('adminhtml/renew/reneworder')}')", // @codingStandardsIgnoreLine
                        'class'     => 'renew'
                    ), 0, 100, 'header', 'header'
                );
            }
        }
    }

    /**
     * @param $event
     */
    public function adminLoginAfter($event)
    {
        if (Mage::helper('viabill')->isExtensionEnabled()) {
            $notification = Mage::getModel('viabill/viabill_myViaBill');
            $notification->registerNotifications();
        }
    }
}
