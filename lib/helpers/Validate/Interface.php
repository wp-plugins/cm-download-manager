<?php


interface CMDM_Validate_Interface {
    /**
     * Get errors
     * @return array array of errors
     */
    public function getErrors();
    /**
     * Check if value is valid
     * @param mixed $value
     * @return boolean is valid
     */
    public function isValid($value);
}
?>
