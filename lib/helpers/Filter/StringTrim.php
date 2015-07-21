<?php


require_once 'Interface.php';

class CMDM_Filter_StringTrim implements CMDM_Filter_Interface{
    public function filter($value) {
        return trim($value);
    }
}

?>
