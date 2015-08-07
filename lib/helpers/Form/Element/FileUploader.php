<?php


require_once CMDM_PATH . "/lib/helpers/Form/Element.php";


class CMDM_Form_Element_FileUploader extends CMDM_Form_Element {
    public function isValid($value, $context = array(), $showError = false) {
        $value = isset($_FILES[$this->getId()]) && empty($_FILES[$this->getId()]['error'])?$_FILES[$this->getId()]:'';
        $this->setValue($value);
        return parent::isValid($value, $context, $showError);
    }
    public function render() {
        $html = '<input type="file" name="'.$this->getId().'" '.$this->_getClassName().$this->_getStyle().' />';
        $value = $this->getValue();
        if (!empty($value)) {
            if (is_array($value))
                $value = $value['name'];
            $html.=$value;
        }
        return $html;
    }

}

?>
