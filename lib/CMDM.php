<?php

include_once CMDM_PATH . '/lib/models/GroupDownloadPage.php';
include_once CMDM_PATH . '/lib/controllers/BaseController.php';

class CMDM {
    public static function init() {
        CMDM_GroupDownloadPage::init();
        add_action('init', array('CMDM_BaseController', 'bootstrap'));
    }
    public static function install() {
        CMDM_GroupDownloadPage::init();
        CMDM_GroupDownloadPage::registerPostTypes();
        CMDM_GroupDownloadPage::registerTaxonomies();
    }
    public static function uninstall() {
        
    }
   
}

?>
