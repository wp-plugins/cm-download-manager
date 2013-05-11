<?php


require_once CMDM_PATH."/lib/helpers/Validate/Interface.php";

class CMDM_Validate_Decimal implements CMDM_Validate_Interface {
    protected $_errors = array();
    const NOT_DIGITS = '%label% must contain positive numbers only';
    public function getErrors() {
        return $this->_errors;
    }
    public function isValid($value) {
        if (!is_numeric($value) || $value<0) {
            $this->_errors[] = self::NOT_DIGITS;
            return false;
        } else
            return true;
    }
}

?>
