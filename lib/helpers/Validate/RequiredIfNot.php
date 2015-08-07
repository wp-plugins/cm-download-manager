<?php


require_once CMDM_PATH . "/lib/helpers/Validate/Interface.php";


class CMDM_Validate_RequiredIfNot implements CMDM_Validate_Interface {

    protected $_errors = array();
    protected $_tokens = array();
    const IS_EMPTY = '%label% is required.';

    public function __construct(array $tokens) {
        $this->_tokens = $tokens;
    }

    public function getErrors() {
        return $this->_errors;
    }

    public function isValid($value, $context = null) {
        $emptyTokens = false;
        foreach ($this->_tokens as $token) {
            if (isset($context[$token]) && !empty($context[$token])) {
                $emptyTokens = true;
            }
        }
        if (!$emptyTokens && empty($value)) {
            $this->_errors[] = self::IS_EMPTY;
            return false;
        }
        else
            return true;
    }

}

?>
