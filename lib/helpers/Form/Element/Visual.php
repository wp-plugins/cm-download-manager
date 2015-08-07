<?php


require_once CMDM_PATH . "/lib/helpers/Form/Element.php";


class CMDM_Form_Element_Visual extends CMDM_Form_Element {
    protected function _init() {
        $this->addFilter('stripMaliciousTags');
    }
    public function setSize($rows, $cols) {
        $this->_attribs['rows'] = $rows;
        $this->_attribs['cols'] = $cols;
        return $this;
    }

    protected function _getSize() {
        $args = array();
        if (!empty($this->_attribs['rows'])) {
            $args['textarea_rows'] =$this->_attribs['rows'];
        }
        if (!empty($this->_attribs['cols'])) {
            $args['textarea_cols'] = $this->_attribs['cols'];
        }
        return $args;
    }
 protected function _getMaxLength() {
        if (!empty($this->_attribs['maxLength'])) {
            return ' maxlength="' . $this->_attribs['maxLength'] . '"';
        } else {
            return '';
        }
    }
    public function render() {
        ob_start();
        $args = array('media_buttons'=>false,  'teeny'=>true, 'tinymce'=>true);
        $args = array_merge($args, $this->_getSize());
        wp_editor($this->getValue(), $this->getId(), $args);
        $html = '<div '.$this->_getClassName().'>'.ob_get_contents().'</div>';
        ob_end_clean();
        return $html;
    }
      public function getValue() {
        return stripslashes($this->_value);
    }

}

?>
