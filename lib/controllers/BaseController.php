<?php

abstract class CMDM_BaseController {
    const TITLE_SEPARATOR = '&gt;';
    const MESSAGE_SUCCESS = 'success';
    const MESSAGE_ERROR = 'error';
    const ADMIN_SETTINGS = 'CMDM_admin_settings';
    const ADMIN_ABOUT = 'CMDM_admin_about';
    const OPTION_TITLES = 'CMDM_panel_titles';
    protected static $_titles = array();
    protected static $_fired = false;
    protected static $_pages = array();
    protected static $_params = array();
    protected static $_errors = array();
    protected static $_messages = array(self::MESSAGE_SUCCESS => array(), self::MESSAGE_ERROR => array());
    protected static $_customPostTypes = array();

    public static function init() {

        add_action('init', array(get_class(), 'registerPages'), 2);
    }

    protected static function _addAdminPages() {
        add_action('CMDM_custom_post_type_nav', array(get_class(), 'addCustomPostTypeNav'), 1, 1);
        add_action('CMDM_custom_taxonomy_nav', array(get_class(), 'addCustomTaxonomyNav'), 1, 1);
        add_action('admin_menu', array(get_class(), 'registerAdminPages'));
    }

    public static function initSessions() {
        if (!session_id()) {
            session_start();
        }
        if (isset($_SESSION['CMDM_messages'])) {
            self::$_messages = $_SESSION['CMDM_messages'];
        }
        add_action('wp_logout', array(get_class(), 'endSessions'));
        add_action('wp_login', array(get_class(), 'endSessions'));
    }

    public static function endSessions() {
        self::initSessions();
        if (session_id()) {
            session_regenerate_id(true);
            session_destroy();
            unset($_SESSION);
            self::initSessions();
        }
    }

    public static function initialize() {
        
    }

    public static function registerPages() {
//        flush_rewrite_rules();
        add_action('generate_rewrite_rules', array(get_class(), 'registerRewriteRules'));

//        flush_rewrite_rules();
        add_filter('query_vars', array(get_class(), 'registerQueryVars'));
        add_filter('wp_title', array(get_class(), '_showPageTitle'), 1, 3);
        add_filter('the_posts', array(get_class(), 'editQuery'), 10, 1);
        add_filter('the_content', array(get_class(), 'showPageContent'), 10, 1);
        if (!is_admin())
            wp_enqueue_style('CMDM-css', CMDM_URL . '/views/resources/app.css');
    }

    public static function registerRewriteRules($rules) {
        $newRules = array();
        $additional = array();
        foreach (self::$_pages as $page) {
            if (is_array($page['slug'])) {
                foreach ($page['slug'] as $slug) {
                    if (strpos($slug, '/') === false)
                        $additional['^' . $slug . '(?=\/|$)'] = 'index.php?' . $page['query_var'] . '=1';
                    else
                        $newRules['^' . $slug . '(?=\/|$)'] = 'index.php?' . $page['query_var'] . '=1';
                }
            } else
                $newRules['^' . $page['slug'] . '(?=\/|$)'] = 'index.php?' . $page['query_var'] . '=1';
        }
        $rules->rules = $newRules + $rules->rules + $additional;
        return $rules->rules;
    }

    public static function flush_rules() {
        $rules = get_option('rewrite_rules');
        foreach (self::$_pages as $page) {
            if (!isset($rules['^' . $slug . '(?=\/|$)'])) {
                global $wp_rewrite;
                $wp_rewrite->flush_rules();
                return;
            }
        }
    }

    public static function registerQueryVars($query_vars) {
        self::flush_rules();
        foreach (self::$_pages as $page) {
            $query_vars[] = $page['query_var'];
        }
        return $query_vars;
    }

    protected static function _registerAction($query_var, $args = array()) {
        $slug = $args['slug'];
        $contentCallback = isset($args['contentCallback']) ? $args['contentCallback'] : null;
        $headerCallback = isset($args['headerCallback']) ? $args['headerCallback'] : null;
        $title = !empty($args['title']) ? $args['title'] : ucfirst($slug);
        $titleCallback = isset($args['titleCallback']) ? $args['titleCallback'] : null;
        self::$_pages[$query_var] = array(
            'query_var' => $query_var,
            'slug' => $slug,
            'title' => $title,
            'titleCallback' => $titleCallback,
            'contentCallback' => $contentCallback,
            'headerCallback' => $headerCallback,
            'viewPath' => $args['viewPath'],
            'controller' => $args['controller'],
            'action' => $args['action']
        );
    }

    /**
     * Locate the template file, either in the current theme or the public views directory
     *
     * @static
     * @param array $possibilities
     * @param string $default
     * @return string
     */
    protected static function locateTemplate($possibilities, $default = '') {

// check if the theme has an override for the template
        $theme_overrides = array();
        foreach ($possibilities as $p) {
            $theme_overrides[] = 'CMDM/' . $p . '.phtml';
        }
        if ($found = locate_template($theme_overrides, FALSE)) {
            return $found;
        }

// check for it in the public directory
        foreach ($possibilities as $p) {
            if (file_exists(CMDM_PATH . '/views/frontend/' . $p . '.phtml')) {
                return CMDM_PATH . '/views/frontend/' . $p . '.phtml';
            }
        }

// we don't have it
        return $default;
    }

    public static function _showPageTitle($title, $sep = '', $seplocation = 'right') {
        foreach (self::$_pages as $page) {
            if (get_query_var($page['query_var']) == 1) {
                if (!empty($page['titleCallback']))
                    $title = call_user_func($page['titleCallback']);
                else
                    $title = self::$_titles[$page['controller'] . '-' . $page['action']] ? self::$_titles[$page['controller'] . '-' . $page['action']] : $page['title'];
                if (!empty($sep)) {
                    $title = str_replace(self::TITLE_SEPARATOR, $sep, $title);
                    if ($seplocation == 'right')
                        $title.=' ' . $sep . ' ';
                    else
                        $title = ' ' . $sep . ' ' . $title;
                }
                break;
            }
        }
        return $title;
    }

    public static function editQuery($posts) {
        if (!self::$_fired) {
            global $wp_query;
            foreach (self::$_pages as $page) {
                if (get_query_var($page['query_var']) == 1) {
                    if (!empty($page['headerCallback'])) {
                        self::$_fired = true;
                        call_user_func($page['headerCallback']);
                    }
//create a fake post
                    $post = new stdClass;
                    $post->post_author = 1;
                    $post->post_name = $page_slug;
                    $post->guid = get_bloginfo('wpurl' . '/' . $page['slug']);
                    $post->post_title = self::_showPageTitle($page['title']);
//put your custom content here
                    $post->post_content = 'Content Placeholder';
//just needs to be a number - negatives are fine
                    $post->ID = -42;
                    $post->post_status = 'static';
                    $post->comment_status = 'closed';
                    $post->ping_status = 'closed';
                    $post->comment_count = 0;
//dates may need to be overwritten if you have a "recent posts" widget or similar - set to whatever you want
                    $post->post_date = current_time('mysql');
                    $post->post_date_gmt = current_time('mysql', 1);

                    $posts = NULL;
                    $posts[] = $post;

                    $wp_query->is_page = true;
                    $wp_query->is_singular = true;
                    $wp_query->is_home = false;
                    $wp_query->is_archive = false;
                    $wp_query->is_category = false;
                    unset($wp_query->query["error"]);
                    $wp_query->query_vars["error"] = "";
                    $wp_query->is_404 = false;
                    add_filter('template_include', array(get_class(), 'overrideBaseTemplate'));
                    break;
                }
            }
        }
        return $posts;
    }

    public static function overrideBaseTemplate($template) {
        $template = self::locateTemplate(array(
                    'page'
                        ), $template);
        return $template;
    }
   
    public static function showPageContent($content) {
        foreach (self::$_pages as $page) {
            if (get_query_var($page['query_var']) == 1) {
                remove_filter('the_content', 'wpautop');
                if (!empty(self::$_errors)) {
                    $viewParams = call_user_func(array('CMDM_ErrorController', 'errorAction'));
                    ob_start();
                    echo self::_loadView('error', $viewParams);
                    $content = ob_get_contents();
                    ob_end_clean();
                } else {
                    $viewParams = array();
                    if (!empty($page['contentCallback']))
                        $viewParams = call_user_func($page['contentCallback']);
                    ob_start();
                    echo self::_loadView('messages', array('messages' => self::_getMessages()));
                    echo self::_loadView($page['viewPath'], $viewParams);
                    $content = ob_get_contents();
                    ob_end_clean();
                }
                break;
            }
        }
        return $content;
    }

    protected static function _loadView($_name, $_params = array()) {
        $path = CMDM_PATH . '/views/frontend/' . $_name . '.phtml';
        $template = self::locateTemplate(array($_name), $path);
//        if (!file_exists($path))
//            throw new Exception('You do not have a view file for ' . $_name);
        if (!empty($_params))
            extract($_params);
        ob_start();
        require($template);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    protected static function _getSlug($controller, $action, $single = false) {
        if ($action == 'index')
            if ($single)
                return $controller;
            else
                return array(
                    $controller . '/' . $action,
                    $controller
                );
        else
            return $controller . '/' . $action;
    }

    protected static function _getTitle($controller, $action, $hasBody = false) {
        $title = apply_filters('CMDM_title_controller', ucfirst($controller)) . ' ' . self::TITLE_SEPARATOR . ' ' . apply_filters('CMDM_title_action', ucfirst($action));
        $titles = self::$_titles;
        if (!isset(self::$_titles[$controller . '-' . $action]) && $hasBody)
            self::$_titles[$controller . '-' . $action] = $title;

        return $title;
    }

    protected static function _getQueryArg($controller, $action) {
        return "CMDM-{$controller}-{$action}";
    }

    protected static function _getViewPath($controller, $action) {
        return $controller . '/' . $action;
    }

    public static function bootstrap() {
        self::_addAdminPages();
        self::$_titles = get_option(self::OPTION_TITLES, array());
        $controllersDir = dirname(__FILE__);
        foreach (scandir($controllersDir) as $name) {
            if ($name != '.' && $name != '..' && $name != basename(__FILE__) && strpos($name, 'Controller.php') !== false) {
                $controllerName = substr($name, 0, strpos($name, 'Controller.php'));
                $controllerClassName = CMDM_PREFIX . $controllerName . 'Controller';
                $controller = strtolower($controllerName);
                include_once $controllersDir . DIRECTORY_SEPARATOR . $name;
                $controllerClassName::initialize();
//                if (!is_admin()) {
                $args = array();
                foreach (get_class_methods($controllerClassName) as $methodName) {
                    if (strpos($methodName, 'Action') !== false && substr($methodName, 0, 1) != '_') {
                        $action = substr($methodName, 0, strpos($methodName, 'Action'));
                        $query_arg = self::_getQueryArg($controller, $action);
                        $newArgs = array(
                            'query_arg' => self::_getQueryArg($controller, $action),
                            'slug' => self::_getSlug($controller, $action),
                            'title' => self::_getTitle($controller, $action, true),
                            'viewPath' => self::_getViewPath($controller, $action),
                            'contentCallback' => array($controllerClassName, $methodName),
                            'controller' => $controller,
                            'action' => $action
                        );
                        if (!isset($args[$query_arg]))
                            $args[$query_arg] = array();
                        $args[$query_arg] = array_merge($args[$query_arg], $newArgs);
                    } elseif (strpos($methodName, 'Header') !== false && substr($methodName, 0, 1) != '_') {
                        $action = substr($methodName, 0, strpos($methodName, 'Header'));
                        $query_arg = self::_getQueryArg($controller, $action);
                        $newArgs = array(
                            'query_arg' => self::_getQueryArg($controller, $action),
                            'slug' => self::_getSlug($controller, $action),
                            'title' => self::_getTitle($controller, $action),
                            'viewPath' => self::_getViewPath($controller, $action),
                            'headerCallback' => array($controllerClassName, $methodName),
                            'controller' => $controller,
                            'action' => $action
                        );
                        if (!isset($args[$query_arg]))
                            $args[$query_arg] = array();
                        $args[$query_arg] = array_merge($args[$query_arg], $newArgs);
                    }
                    elseif (strpos($methodName, 'Title') !== false && substr($methodName, 0, 1) != '_') {
                        $action = substr($methodName, 0, strpos($methodName, 'Title'));
                        $query_arg = self::_getQueryArg($controller, $action);
                        $newArgs = array(
                            'query_arg' => self::_getQueryArg($controller, $action),
                            'slug' => self::_getSlug($controller, $action),
                            'title' => self::_getTitle($controller, $action),
                            'viewPath' => self::_getViewPath($controller, $action),
                            'titleCallback' => array($controllerClassName, $methodName),
                            'controller' => $controller,
                            'action' => $action
                        );
                        if (!isset($args[$query_arg]))
                            $args[$query_arg] = array();
                        $args[$query_arg] = array_merge($args[$query_arg], $newArgs);
                    }
                }
                foreach ($args as $query_arg => $data) {
                    self::_registerAction($query_arg, $data);
                }
//                }
            }
        }

        self::registerPages();
        self::initSessions();
    }

    protected static function _getHelper($name, $params = array()) {
        $name = ucfirst($name);
        include_once CMDM_PATH . '/lib/helpers/' . $name . '.php';
        $className = CMDM_PREFIX . $name;
        return new $className($params);
    }

    protected static function _isPost() {
        return strtolower($_SERVER['REQUEST_METHOD']) == 'post';
    }

    public static function getUrl($controller, $action, $params = array()) {
        $paramsString = '';
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $paramsString.='/' . urlencode($key) . '/' . urlencode($value);
            }
        }
        return home_url(self::_getSlug($controller, $action, true)) . $paramsString;
    }

    /**
     * Get action param (from $_GET or uri - /name/value)
     * @param string $key
     * @return string 
     */
    public static function _getParam($name) {
        if (empty(self::$_params)) {
            $req_uri = $_SERVER['REQUEST_URI'];
            $home_path = parse_url(home_url());
            if (isset($home_path['path']))
                $home_path = $home_path['path'];
            else
                $home_path = '';
            $home_path = trim($home_path, '/');
            $req_uri = trim($req_uri, '/');
            $req_uri = preg_replace("|^$home_path|", '', $req_uri);
            $req_uri = trim($req_uri, '/');
            $parts = explode('/', $req_uri);
            if (!empty($parts)) {
                for ($i = count($parts) - 1; $i > 1; $i-=2) {
                    $params[$parts[$i - 1]] = $parts[$i];
                }
                self::$_params = $params + $_REQUEST;
            }
        }
        return isset(self::$_params[$name]) ? self::$_params[$name] : '';
    }

    protected static function _addError($msg) {
        self::$_errors[] = $msg;
    }

    protected static function _getErrors() {
        $errors = self::$_errors;
        self::$_errors = array();
        return $errors;
    }

    protected static function _saveMessages() {
        $_SESSION['CMDM_messages'] = self::$_messages;
    }

    protected static function _getMessages($type = null) {
        $list = array();
        if ($type !== null && isset(self::$_messages[$type])) {
            $list = self::$_messages[$type];
            self::$_messages[$type] = array();
        } else {
            $list = self::$_messages;
            self::$_messages = array(self::MESSAGE_SUCCESS => array(), self::MESSAGE_ERROR => array());
        }
        self::_saveMessages();
        return $list;
    }

    protected static function _addMessage($type, $msg) {
        if (isset(self::$_messages[$type])) {
            self::$_messages[$type][] = $msg;
            self::_saveMessages();
        }
    }

    public static function _userRequired() {
        if (!is_user_logged_in()) {
            self::_addError(__('You have to be logged in to see this page', 'cm-download-manager').' <a href="' . wp_login_url($_SERVER['REQUEST_URI']) . '">'.__('Log in', 'cm-download-manager').'</a>');
            return false;
        }
        return true;
    }

    public static function registerAdminPages() {

        add_submenu_page(apply_filters('CMDM_admin_parent_menu', 'options-general.php'), 'CM Downloads Settings', 'Downloads Settings', 'manage_options', self::ADMIN_SETTINGS, array(get_class(), 'displaySettingsPage'));
        add_submenu_page(apply_filters('CMDM_admin_parent_menu', 'options-general.php'), 'About', 'About', 'manage_options', self::ADMIN_ABOUT, array(get_class(), 'displayAboutPage'));
    }

    public static function displaySettingsPage() {
                wp_enqueue_script('jquery');
        wp_enqueue_script('media-upload');
wp_enqueue_script('thickbox');
wp_register_script('cmdm-admin-upload', CMDM_URL.'/views/resources/js/admin.js', array('jquery','media-upload','thickbox'));
wp_enqueue_script('cmdm-admin-upload');
wp_enqueue_style('thickbox');
        $messages = array();
        if (!empty($_POST['titles'])) {
            self::$_titles = array_map('stripslashes', $_POST['titles']);
            update_option(self::OPTION_TITLES, self::$_titles);
            $messages[] = 'Settings succesfully updated';
        }
        $params = array();
        $params = apply_filters('CMDM_admin_settings', $params);
        extract($params);
        ob_start();
        require(CMDM_PATH . '/views/backend/settings.phtml');
        $content = ob_get_contents();
        ob_end_clean();
        self::displayAdminPage($content);
    }

    public static function getAdminNav() {
        global $submenu, $plugin_page, $pagenow;
        ob_start();
        $submenus = array();
        if (isset($submenu[apply_filters('CMDM_admin_parent_menu', 'options-general.php')])) {
            $thisMenu = $submenu[apply_filters('CMDM_admin_parent_menu', 'options-general.php')];
            foreach ($thisMenu as $item) {
                $slug = $item[2];
                $slugParts = explode('?', $slug);
                $name = '';
                if (count($slugParts)>1) $name = $slugParts[0];
                $isCurrent = ($slug == $plugin_page || (!empty($name) && $name===$pagenow));
                $url = (strpos($item[2], '.php') !== false) ? $slug : get_admin_url('', 'admin.php?page=' . $slug);
                $submenus[] = array(
                    'link' => $url,
                    'title' => $item[0],
                    'current' => $isCurrent
                );
            }
            require(CMDM_PATH . '/views/backend/nav.phtml');
        }
        $nav = ob_get_contents();
        ob_end_clean();
        return $nav;
    }
    public static function displayAdminPage($content) {
                $nav = self::getAdminNav();
        require(CMDM_PATH . '/views/backend/template.phtml');
    }
    public static function displayAboutPage() {
        ob_start();
        require(CMDM_PATH . '/views/backend/about.phtml');
        $content = ob_get_contents();
        ob_end_clean();
        self::displayAdminPage($content);
    }
    public static function addCustomTaxonomyNav($taxonomy) {
        add_action('after-' . $taxonomy . '-table', array(get_class(), 'filterAdminNavEcho'), 10, 1);
    }
    public static function filterAdminNavEcho() {
        echo self::getAdminNav();
        ?>
<script>
jQuery(document).ready(function($) {
   $('#col-container').prepend($('#CMDM_admin_nav')); 
});
</script>
<?php
    }
    public static function addCustomPostTypeNav($postType) {
        self::$_customPostTypes[] = $postType;
        add_filter('views_edit-' . $postType, array(get_class(), 'filterAdminNav'), 10, 1);
        add_action('restrict_manage_posts', array(get_class(), 'addAdminStatusFilter'));
    }

    public static function addAdminStatusFilter($postType) {
        global $typenow;
        if (in_array($typenow, self::$_customPostTypes)) {
            $status = get_query_var('post_status');
            ?><select name="post_status">
                <option value="published">Published</option>
                <option value="trash"<?php if ($status == 'trash')
                echo ' selected="selected"'; ?>>Trash</option>
            </select><?php
        }
    }

    public static function filterAdminNav($views = null) {
        global $submenu, $plugin_page, $pagenow;
        $scheme = is_ssl() ? 'https://' : 'http://';
        $adminUrl = str_replace($scheme . $_SERVER['HTTP_HOST'], '', admin_url());
        $homeUrl = home_url();
        $currentUri = str_replace($adminUrl, '', $_SERVER['REQUEST_URI']);
        $submenus = array();
        if (isset($submenu[apply_filters('CMDM_admin_parent_menu', 'options-general.php')])) {
            $thisMenu = $submenu[apply_filters('CMDM_admin_parent_menu', 'options-general.php')];
            foreach ($thisMenu as $item) {
                $slug = $item[2];
                $isCurrent = ($slug == $plugin_page || strpos($item[2], '.php') === strpos($currentUri, '.php'));
                $url = (strpos($item[2], '.php') !== false) ? $slug : get_admin_url('', 'admin.php?page=' . $slug);
                $submenus[$item[0]] =
                        '<a href="' . $url . '" class="' . ($isCurrent ? 'current' : '') . '">' . $item[0] . '</a>';
            }
        }
        return $submenus;
    }

}
?>
