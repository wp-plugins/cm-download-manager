<?php

require_once CMDM_PATH."/lib/helpers/Form/Element/Combobox.php";

class CMDM_Form_Element_Radio extends CMDM_Form_Element_Combobox {
    protected function _renderOptions($prefix = '', $postfix = '') {
        $html = '';
        foreach ($this->getOptions() as $key=>$val) {
            $html .= $prefix.'<input type="radio" id="'.$this->getId().'_'.$key.'" name="'.$this->getId().'"'
                    . ' value="'.$key.'"'.$this->_getClassName().$this->_getStyle().$this->_getReadonly().$this->_getOnClick()
                         . ($this->getValue()==$key?'checked="checked"':''). '/>'
                    . '<label for="'.$this->getId().'_'.$key.'">'.$val.'</label>'.$postfix;
        }
        return $html;
    }
    public function render($prefix = '', $postfix = '') {
        $html = $this->_renderOptions($prefix, $postfix);
        return $html;
    }
}

?>
