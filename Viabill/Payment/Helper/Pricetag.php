<?php
/**
 * Copyright © ViaBill. All rights reserved.
 * See LICENSE.txt for license details.
 */
/**
 * Class Viabill_Payment_Helper_Pricetag
 */
class Viabill_Payment_Helper_Pricetag extends Mage_Payment_Helper_Data
{
    /**
     * Allowed EU countries.
     *
     * @var array
     */
    private $_allowedEUCountries = ['ES'];

    /**
     * @return string
     */
    public function getPricetagScript()
    {
        return Mage::getStoreConfig('payment/viabill_config_form/pricetag_script');
    }

    /**
     * @return string
     */
    public function getDataCurrency()
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode();
    }

    /**
     * @return string
     */
    public function getDataCountryCode()
    {
        $countryCode = Mage::getStoreConfig('general/country/default');
        if (in_array($countryCode, $this->_allowedEUCountries)) {
            return $countryCode;
        } else {
            return null;
        }

        //return Mage::app()->getStore()->getCurrentCurrencyCode();
    }

    /**
     * @return string
     */
    public function getDataLanguage()
    {
        $lang = Mage::getStoreConfig('general/locale/code', Mage::app()->getStore()->getId());
        return strstr($lang, '_', true);
    }

    /**
     * @return boolean
     */
    public function showOnProduct()
    {
        return $this->_isExtensionEnabled() && Mage::getStoreConfigFlag('payment/viabill_price_tag/show_on_product');
    }

    /**
     * @return boolean
     */
    public function showOnCart()
    {
        return $this->_isExtensionEnabled() && Mage::getStoreConfigFlag('payment/viabill_price_tag/show_on_cart');
    }

    /**
     * @return boolean
     */
    public function showOnCheckout()
    {
        return $this->_isExtensionEnabled() && Mage::getStoreConfigFlag('payment/viabill_price_tag/show_on_checkout');
    }

    /**
     * @return string
     */
    public function getGrandTotal()
    {
        return Mage::getModel('checkout/session')->getQuote()->getGrandTotal();
    }

    /**
     * @return boolean
     */
    protected function _isExtensionEnabled()
    {
        return Mage::helper('viabill')->isExtensionEnabled();
    }
}
