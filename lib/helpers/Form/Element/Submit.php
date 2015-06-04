<?php


require_once CMDM_PATH."/lib/helpers/Form/Element.php";

class CMDM_Form_Element_Submit extends CMDM_Form_Element {
    public function render() {
        $html = '<input type="submit" id="'.$this->getId().'" name="'.$this->getId()
                .'" value="'.$this->getValue().'"'
                .$this->_getClassName().$this->_getStyle().$this->_getReadonly().$this->_getOnClick().' />';
        return $html;
    }
}

?>
