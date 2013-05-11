<?php

require_once CMDM_PATH."/lib/helpers/Form/Element.php";

class CMDM_Form_Element_Text extends CMDM_Form_Element {
    protected function _init() {
        $this->addFilter('stringTrim')->addFilter('stripTags');
    }
    protected function _getMaxLength() {
        if (!empty($this->_attribs['maxLength'])) {
            return ' maxlength="' . $this->_attribs['maxLength'] . '"';
        } else {
            return '';
        }
    }
    protected function _getAutocomplete() {
                if (isset($this->_attribs['autocomplete'])) {
            return ' autocomplete="off"';
        } else {
            return '';
        }
    }
    public function setSize($size) {
        $this->_attribs['size'] = $size;
        return $this;
    }
     protected function _getSize() {
        $html = '';
        if (!empty($this->_attribs['size'])) {
            $html .= ' maxlength="' . $this->_attribs['size'] . '"';
        }
        return $html;
    }
    public function render() {
        $html = '<input type="text" id="'.$this->getId().'" name="'.$this->getId()
                .'" value="'.htmlspecialchars(stripslashes($this->getValue())).'"'
                .$this->_getClassName().$this->_getStyle().$this->_getReadonly().$this->_getRequired().$this->_getPlaceHolder().$this->_getMaxLength().$this->_getOnClick().$this->_getDisabled().$this->_getSize().$this->_getAutocomplete().' />';
        return $html;
    }
    
    public function getValue() {
        return stripslashes($this->_value);
    }
}

?>
