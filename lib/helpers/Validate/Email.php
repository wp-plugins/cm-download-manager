<?php

require_once CMDM_PATH."/lib/helpers/Validate/Interface.php";

class CMDM_Validate_Email implements CMDM_Validate_Interface {
    protected $_errors = array();
    const NOT_VALID = '%label% is not a valid e-mail address.';
    public function getErrors() {
        return $this->_errors;
    }
    public function isValid($value) {
       if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
           $this->_errors[] = self::NOT_VALID;
           return false;
       } else return true;
               
    }
}


?>
