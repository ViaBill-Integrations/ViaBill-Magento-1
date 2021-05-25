<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */

/**
 * Class Viabill_Payment_Model_Response_AdminNotificationsHandler
 */
class Viabill_Payment_Model_Response_AdminNotificationsHandler
{
    /**
     * @param $commandSubject
     * @param $response
     */
    public function handle($commandSubject, $response)
    {
        if (!empty($response['messages'])) {
            $helper = Mage::helper('viabill');
            $url = Mage::getModel('adminhtml/url')->getUrl('adminhtml/notification');
            /**
             * @var Mage_AdminNotification_Model_Inbox $notoficationsPool
             */
            $notificationsPool = Mage::getModel('adminnotification/inbox');
            $severity = Mage::getStoreConfig('payment/viabill_config_form/admin_notification_severity');
            if (empty($severity)) {
                $severity = Mage_AdminNotification_Model_Inbox::SEVERITY_CRITICAL;
            }

            foreach ($response['messages'] as $message) {
                $notificationsPool->add($severity, $helper->__('ViaBill'), $message, $url, true);
            }

            Mage::getSingleton('core/session')->addSuccess(
                "<a href=\"{$url}\">" . $helper->__('You have new notifications from ViaBill') . '</a>'
            );
        }
    }
}
