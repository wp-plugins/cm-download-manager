<?php


require_once CMDM_PATH . "/lib/helpers/Form/Element.php";

class CMDM_Form_Element_Textarea extends CMDM_Form_Element {
protected function _init() {
        $this->addFilter('stringTrim')->addFilter('stripTags');
    }
    public function setSize($rows, $cols) {
        $this->_attribs['rows'] = $rows;
        $this->_attribs['cols'] = $cols;
        return $this;
    }

    protected function _getSize() {
        $html = '';
        if (!empty($this->_attribs['rows'])) {
            $html .= ' rows="' . $this->_attribs['rows'] . '"';
        }
        if (!empty($this->_attribs['cols'])) {
            $html .= ' cols="' . $this->_attribs['cols'] . '"';
        }
        return $html;
    }
 protected function _getMaxLength() {
        if (!empty($this->_attribs['maxLength'])) {
            return ' maxlength="' . $this->_attribs['maxLength'] . '"';
        } else {
            return '';
        }
    }
    public function render() {
        $html = '<textarea id="' . $this->getId() . '" name="' . $this->getId()
                . '"'
                . $this->_getSize() . $this->_getClassName() . $this->_getStyle() . $this->_getReadonly() . $this->_getRequired() . $this->_getPlaceHolder() . $this->_getOnClick() . $this->_getMaxLength(). ' >'
                . $this->getValue() . '</textarea>';
        return $html;
    }
      public function getValue() {
        return stripslashes($this->_value);
    }

}

?>
