<?php


class CMDM_ErrorController extends CMDM_BaseController {
    public static function errorAction() {
        return array('errors'=>self::_getErrors());
    }
}

?>
