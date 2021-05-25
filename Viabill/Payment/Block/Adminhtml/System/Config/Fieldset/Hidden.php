<?php

class Viabill_Payment_Block_Adminhtml_System_Config_Fieldset_Hidden
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    /**
     * Return collapse state
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return bool
     */
    protected function _getCollapseState($element)
    {
        $extra = Mage::getSingleton('admin/session')->getUser()->getExtra();
        if (isset($extra['configState'][$element->getId()])) {
            return $extra['configState'][$element->getId()];
        }

        return true;
    }

    /**
     * Return header html for fieldset
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getHeaderHtml($element)
    {
        $isHidden = ' style="display:none"';
        if (Mage::helper('viabill')->isViaBillUser()) {
            $isHidden = '';
        }

        if ($element->getIsNested()) {
            $html = '<tr class="nested"><td colspan="4"><div class="' . $this->_getFrontendClass($element) . '"' . $isHidden . '>';
        } else {
            $html = '<div class="' . $this->_getFrontendClass($element) . '">';
        }

        $html .= $this->_getHeaderTitleHtml($element);

        $html .= '<input id="'.$element->getHtmlId() . '-state" name="config_state[' . $element->getId()
            . ']" type="hidden" value="' . (int)$this->_getCollapseState($element) . '" />';
        $html .= '<fieldset class="' . $this->_getFieldsetCss($element) . '" id="' . $element->getHtmlId() . '">';
        $html .= '<legend>' . $element->getLegend() . '</legend>';

        $html .= $this->_getHeaderCommentHtml($element);

        // field label column
        $html .= '<table cellspacing="0" class="form-list"><colgroup class="label" /><colgroup class="value" />';
        if ($this->getRequest()->getParam('website') || $this->getRequest()->getParam('store')) {
            $html .= '<colgroup class="use-default" />';
        }
        $html .= '<colgroup class="scope-label" /><colgroup class="" /><tbody>';

        return $html;
    }
}
