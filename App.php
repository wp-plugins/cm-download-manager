<?php

namespace com\cminds\videolesson;

use com\cminds\videolesson\controller\SettingsController;

use com\cminds\videolesson\shortcode\BookmarksShortcode;
use com\cminds\videolesson\shortcode\PlaylistShortcode;
use com\cminds\videolesson\model\Settings;

class App {
	
	const VERSION = '1.1.0';
	const PREFIX = 'CMVL';
	const MENU_SLUG = 'cmvl';
	const BASE_NAMESPACE = 'com\cminds\videolesson';
	const PLUGIN_NAME = 'CM Video Lesson Manager';
	const PLUGIN_WEBSITE = 'https://plugins.cminds.com/cm-video-lessons-manager-plugin-for-wordpress/';
	
	static private $licensingApi;
	static private $path;

	
	static function bootstrap() {
		
		self::$path = dirname(CMVL_PLUGIN_FILE);
		
		// Auto-load
		spl_autoload_register(array(__CLASS__, 'autoload'));
		
		// Licensing API
		if (self::isPro()) {
			self::$licensingApi = new \CMVL_Cminds_Licensing_API(App::getPluginName(true), App::MENU_SLUG, App::getPluginName(true), CMVL_PLUGIN_FILE,
				array('release-notes' => App::PLUGIN_WEBSITE), '', array(App::getPluginName(true)));
		}
		
		// Class bootstraping
		$classToBootstrap = array_merge(self::getClassNames('controller'), self::getClassNames('model'));
		if (self::isLicenseOk()) {
			$classToBootstrap = array_merge($classToBootstrap, self::getClassNames('shortcode'));
		}
		foreach ($classToBootstrap as $className) {
			$method = array($className, 'bootstrap');
			if (method_exists($className, 'bootstrap') AND is_callable($method)) {
				call_user_func($method);
			}
		}
		
		// Other actions
		add_action('init', array(get_called_class(), 'init'), 1);
		add_action('admin_menu', array(get_called_class(), 'admin_menu'));
		
	}
	
	
	static function init() {
		
		wp_register_script('cmvl-backend', App::url('asset/js/backend.js'), array('jquery'), self::VERSION);
		wp_register_style('cmvl-settings', App::url('asset/css/settings.css'), null, self::VERSION);
		wp_register_style('cmvl-backend', App::url('asset/css/backend.css'), null, self::VERSION);
		
		wp_register_style('cmvl-frontend', App::url('asset/css/frontend.css'), null, self::VERSION);
		wp_register_script('cmvl-utils', App::url('asset/js/utils.js'), array('jquery'), self::VERSION);
		
		wp_register_script('cmvl-playlist', App::url('asset/js/playlist.js'), array('jquery', 'cmvl-utils'), self::VERSION);
		wp_localize_script('cmvl-playlist', 'CMVLSettings', array(
			'ajaxUrl' => admin_url('admin-ajax.php'),
		));
		
	}
	
	
	static function getClassNames($namespaceFragment) {
		$files = scandir(App::path($namespaceFragment));
		foreach ($files as &$name) {
			if (preg_match('/^([a-zA-Z0-9]+)\.php$/', $name, $match)) {
				$name = App::namespaced($namespaceFragment .'\\'. $match[1]);
			} else {
				$name = null;
			}
		}
		return array_filter($files);
	}


	static function autoload($name) {
		if (substr($name, 0, strlen(__NAMESPACE__)) == __NAMESPACE__) {
			$path = str_replace('\\', DIRECTORY_SEPARATOR, substr($name, strlen(__NAMESPACE__)+1, 9999));
			$check = array(App::path($path), App::path('core/'. $path));
			foreach ($check as $file) {
				$file .= '.php';
				if (file_exists($file) AND is_readable($file)) {
					require_once $file;
					return;
				}
			}
		}
	}


	static function admin_menu() {
		$name = App::getPluginName(true);
		$page = add_menu_page($name, $name, 'manage_options', App::MENU_SLUG, create_function('$q', 'return;'), 'dashicons-video-alt2');
	}
	
	
	static function path($path) {
		return self::$path . DIRECTORY_SEPARATOR . $path;
	}
	
	static function prefix($value) {
 		return self::PREFIX . $value;
	}
	
	static function url($url) {
		return trailingslashit(plugins_url('', CMVL_PLUGIN_FILE)) . $url;
	}
	
	static function namespaced($name) {
		return self::BASE_NAMESPACE . '\\' . $name;
	}
	
	static function shortClassName($name, $suffix = '') {
		preg_match('#^(\w+\\\\)*(\w+)'. $suffix .'$#', $name, $match);
		if (!empty($match[2])) return $match[2];
	}
	
	static function isPro() {
		return class_exists('CMVL_Cminds_Licensing_API');
	}
	
	static function isLicenseOk() {
		return (!self::isPro() OR self::$licensingApi->isLicenseOk());
	}
	
	static function getPluginName($full = false) {
		return self::PLUGIN_NAME . (($full && App::isPro()) ? ' Pro' : '');
	}

	
}
