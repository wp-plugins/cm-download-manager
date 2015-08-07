<?php
include_once CMDM_PATH . '/lib/models/GroupDownloadPage.php';
include_once CMDM_PATH . '/lib/controllers/BaseController.php';
class CMDM
{
	
	const TEXT_DOMAIN = 'cm-download-manager';

    public static function init()
    {
        CMDM_GroupDownloadPage::init();
        add_action('init', array('CMDM_BaseController', 'bootstrap'));
        if(get_option('CMDM_afterActivation') == 1)
        {
            add_action('admin_notices', array(get_class(), 'showProMessages'));
        }
    }

    public static function install()
    {
        CMDM_GroupDownloadPage::init();
        CMDM_GroupDownloadPage::registerPostTypes();
        CMDM_GroupDownloadPage::registerTaxonomies();
        update_option('CMDM_afterActivation', 1);
    }

    public static function uninstall()
    {

    }

	public static function __($msg)
    {
        return __($msg, self::TEXT_DOMAIN);
    }
    
    public static function showProMessages()
    {
        /*
         *  Only show to admins
         */
        if(current_user_can('manage_options'))
        {
            ?>
            <div id="message" class="updated fade">
                <p><strong>New !! A Pro version of CM Downloads is <a href="https://www.cminds.com/store/downloadsmanager/" target="_blank">available here</a></strong></p>
            </div><?php
            delete_option('CMDM_afterActivation');
        }
    }

}