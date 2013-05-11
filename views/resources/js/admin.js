jQuery(document).ready(function() {

jQuery('#upload_default_screenshot_button').click(function() {
 formfield = jQuery('#upload_default_screenshot').attr('name');
 tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
 return false;
});

window.send_to_editor = function(html) {
 imgurl = jQuery('img',html).attr('src');
 jQuery('#upload_default_screenshot').val(imgurl);
 jQuery('#upload_default_screenshot_img').attr('src', imgurl);
 tb_remove();
}

});