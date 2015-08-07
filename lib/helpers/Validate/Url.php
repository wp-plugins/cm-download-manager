<?php


require_once CMDM_PATH . "/lib/helpers/Validate/Interface.php";


class CMDM_Validate_Url implements CMDM_Validate_Interface {

    protected $_errors = array();

    const NOT_ZIP = '%label% is not valid URL address';

    public function getErrors() {
        return $this->_errors;
    }

    public function isValid($value) {
        if(preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $value)){
                       return true;
        }else{
             $this->_errors[] = self::NOT_ZIP;
             return false;
        }

    }

}

?>
