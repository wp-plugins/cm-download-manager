<?php


require_once CMDM_PATH."/lib/helpers/Form/Element.php";

class CMDM_Form_Element_Combobox extends CMDM_Form_Element {
    protected $_options = array();
    public function setOptions($options) {
        $this->_options = $options;
        return $this;
    }
    public function getOptions() {
        $options = $this->_options;
        if ($this->getPlaceHolder()) {
            array_unshift($options, $this->getPlaceHolder());
        }
        return $options;
    }
    protected function _renderOptions() {
        $html = '';
        foreach ($this->getOptions() as $key=>$val) {
            $html .= '<option value="'.($key===0?'':$key).'"';
            if ($key == $this->getValue()) $html .= ' selected="selected"';
            $html .= '>'.$val.'</option>';
        }
        return $html;
    }
    public function render() {
        $html = '<select id="'.esc_attr($this->getId()).'" name="'.esc_attr($this->getId())
                .'"'.$this->_getClassName().$this->_getStyle().$this->_getReadonly().$this->_getDisabled().$this->_getOnClick().$this->_getRequired().'>';
        $html .= $this->_renderOptions();
        $html .= '</select>';
        return $html;
    }
}

?>
