<?php


require_once CMDM_PATH."/lib/helpers/Validate/Interface.php";

class CMDM_Validate_Digits implements CMDM_Validate_Interface {
    protected $_errors = array();
    const NOT_DIGITS = '%label% can contain only digits';
    public function getErrors() {
        return $this->_errors;
    }
    public function isValid($value) {
        if (strspn($value, "0123456789") != strlen($value)) {
            $this->_errors[] = self::NOT_DIGITS;
            return false;
        } else
            return true;
    }
}

?>
