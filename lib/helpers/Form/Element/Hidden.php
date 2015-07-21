<?php


require_once CMDM_PATH."/lib/helpers/Form/Element.php";

class CMDM_Form_Element_Hidden extends CMDM_Form_Element {
    public function render() {
        $html = '<input type="hidden" id="'.$this->getId().'" name="'.$this->getId()
                .'" value="'.htmlentities(stripslashes($this->getValue())).'"'
                .$this->_getClassName()
                .' />';
        return $html;
    }
        public function __toString() {
                return '<tr class="CMDM-form-hidden"><td>&nbsp;</td><td>'.$this->render().'</td></tr>';
    }
}

?>
