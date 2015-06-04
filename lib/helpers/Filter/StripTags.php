<?php


require_once 'Interface.php';

class CMDM_Filter_StripTags implements CMDM_Filter_Interface{
    public function filter($value) {
        return strip_tags($value);
    }
}

?>
