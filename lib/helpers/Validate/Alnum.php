<?php


require_once CMDM_PATH."/lib/helpers/Validate/Interface.php";

class CMDM_Validate_Alnum implements CMDM_Validate_Interface {
    protected $_errors = array();
    const NOT_ALNUM = '%label% can contain only letters and digits';
    public function getErrors() {
        return $this->_errors;
    }
    public function isValid($value) {
        if (!preg_match('/^[\p{L}[:alnum:]]+$/u',$value)) {
            $this->_errors[] = self::NOT_ALNUM;
            return false;
        } else
            return true;
    }
}


?>
