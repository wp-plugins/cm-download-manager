<?php


require_once 'Interface.php';

class CMDM_Filter_StripMaliciousTags implements CMDM_Filter_Interface{
    public function filter($value) {
        return wp_filter_kses($value);
    }
}

?>
