<?php
function CMDM_number_of_downloads($id = 0) {
    if ($id==0) {
        global $post;
        $id = $post->ID;
    }
    $download = CMDM_GroupDownloadPage::getInstance($id);
    return $download->getNumberOfDownloads();
}
function CMDM_update_date($id = 0) {
    if ($id==0) {
        global $post;
        $id = $post->ID;
    }
    $download = CMDM_GroupDownloadPage::getInstance($id);
    return $download->getUpdated();
}
function CMDM_is_top_query() {
    global $wp_query;
    return $wp_query->is_top===true;
}
function CMDM_get_url($controller, $action = '', $params = array()) {
    return CMDM_BaseController::getUrl($controller, $action, $params);
}
function CMDM_get_screenshots($id = 0) {
    if ($id==0) {
        global $post;
        $id = $post->ID;
    }
    $download = CMDM_GroupDownloadPage::getInstance($id);
    return $download->getScreenshots();
}
function CMDM_get_default_screenshot() {
    return CMDM_DownloadController::getDefaultScreenshot();
}

?>
