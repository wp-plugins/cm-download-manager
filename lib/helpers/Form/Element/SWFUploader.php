<?php

require_once CMDM_PATH . "/lib/helpers/Form/Element.php";

class CMDM_Form_Element_SWFUploader extends CMDM_Form_Element {

    public function _init() {
//        wp_deregister_script('swfupload');
        wp_register_script('CMDM-swfupload', CMDM_URL . '/views/resources/swfupload/swfupload.js');
        wp_enqueue_script('CMDM-swfupload');
        wp_register_script('CMDM-swfupload-queue', CMDM_URL . '/views/resources/swfupload/swfupload.queue.js', array('CMDM-swfupload'));
        wp_enqueue_script('CMDM-swfupload-queue');
        wp_register_script('CMDM-fileprogress', CMDM_URL . '/views/resources/swfupload/fileprogress.js', array('CMDM-swfupload'));
        wp_enqueue_script('CMDM-fileprogress');
        wp_register_script('CMDM-swfupload-handlers', CMDM_URL . '/views/resources/swfupload/handlers.js', array('CMDM-swfupload', 'CMDM-fileprogress'));
        wp_enqueue_script('CMDM-swfupload-handlers');
        wp_enqueue_script('json2');
//        wp_enqueue_script('swfupload-handlers');
    }

    public function setValue($value) {
        if (!empty($value) && !is_array($value)) {
            $decodedValue = json_decode(stripslashes($value));
            if (empty($decodedValue)) {
                parent::setValue('');
                return;
            }
        }
        parent::setValue($value);
    }

    public function isValid($value, $context = array(), $showError = false) {
        if (!empty($value) && !is_array($value))
            $_value = json_decode(stripslashes($value));
        else
            $_value = $value;
        if ($this->isRequired() && empty($_value)) {
            if ($showError)
                $this->addError(sprintf(__('%s needs to be uploaded', 'cm-download-manager'), $this->getLabel()));
            return false;
        }
        $fileUploadLimit = isset($this->_attribs['fileUploadLimit']) ? $this->_attribs['fileUploadLimit'] : 0;
        if ($fileUploadLimit > 0) {
            if (is_array($_value) && count($_value) > $fileUploadLimit) {
                if ($showError)
                    $this->addError(sprintf(__e('Limit of uploaded files (%s) has been exceeded!', 'cm-download-manager'), $fileUploadLimit));
                return false;
            }
        }
        return parent::isValid($value, $context);
    }

    public function renderScript() {
        $uploadUrl = $this->_attribs['uploadUrl'];
        $sizeLimit = $this->_attribs['sizeLimit'];
        $fileTypes = $this->_attribs['fileTypes'];
        $fileTypesDescription = $this->_attribs['fileTypesDescription'];
        $fileUploadLimit = isset($this->_attribs['fileUploadLimit']) ? $this->_attribs['fileUploadLimit'] : 0;
        $fileUploadLeft = $fileUploadLimit;
        if ($fileUploadLeft > 0) {
            $value = $this->getValue();
            if (!is_array($value))
                $value = json_decode(stripslashes($value));
            $fileUploadLeft-=count($value);
        }
        ?>
        <script>
            var <?php echo $this->getId(); ?>swfu;
            var <?php echo $this->getId(); ?>swfuFirstTime = true;
            jQuery(document).ready(function() {
                var settings = {
                    flash_url : "<?php echo CMDM_URL . '/views/resources/swfupload/swfupload.swf'; ?>",
                    file_post_name: "upload",
                    upload_url: "<?php echo $uploadUrl; ?>",
                    file_size_limit : "<?php echo $sizeLimit; ?>",
                    file_types : "<?php echo $fileTypes; ?>",
                    file_types_description : "<?php echo $fileTypesDescription; ?>",
                    file_upload_left : <?php echo $fileUploadLeft; ?>,
                    file_upload_limit: <?php echo $fileUploadLimit; ?>,
                    file_queue_limit : 0,
                    custom_settings : {
                        progressTarget : "<?php echo $this->getId(); ?>fsUploadProgress"
                    },
                    debug: false,

                    // Button settings
                    button_image_url: "<?php echo CMDM_URL . '/views/resources/imgs/uploadButton.png'; ?>",
                    button_width: "61",
                    button_height: "22",
                    button_placeholder_id: "<?php echo $this->getId(); ?>_button",
                        				
                    // The event handler functions are defined in handlers.js
                    <?php if (count($value)>0): ?>
                        file_dialog_start_handler: <?php echo $this->getId(); ?>checkStartValues,
                        <?php endif; ?>
                    file_queued_handler : fileQueued,
                    file_queue_error_handler : fileQueueError,
                    file_dialog_complete_handler : fileDialogComplete,
                    upload_start_handler : uploadStart,
                    upload_progress_handler : uploadProgress,
                    upload_error_handler : uploadError,
                    upload_success_handler : <?php echo $this->getId(); ?>uploadSuccess,
                    upload_complete_handler : uploadComplete,
                    queue_complete_handler : queueComplete	// Queue plugin event
                };

                <?php echo $this->getId(); ?>swfu = new SWFUpload(settings);	
                
            });
            function <?php echo $this->getId(); ?>checkStartValues() {
                            if (<?php echo $this->getId(); ?>swfuFirstTime) {
                            var stats = <?php echo $this->getId(); ?>swfu.getStats();
                            stats.successful_uploads = <?php echo count($value); ?>;
                            <?php echo $this->getId(); ?>swfu.setStats(stats);
                            <?php echo $this->getId(); ?>swfuFirstTime = false;
                            }
                        }
            function <?php echo $this->getId(); ?>uploadSuccess(file, serverData) {
//                try {
                    var progress = new FileProgress(file, "<?php echo $this->getId(); ?>fsUploadProgress");
                    serverData = jQuery.parseJSON(serverData);
                    if (serverData.success==1) {
                        var currentFiles = jQuery.parseJSON(jQuery("#<?php echo $this->getId(); ?>").val());
                        currentFiles.push(serverData.message);
                        jQuery("#<?php echo $this->getId(); ?>").val(JSON.stringify(currentFiles));
                        progress.setName(serverData.message, "<?php echo CMDM_get_url('cmdownload', 'screenshot', array('size'=>'196x60', 'img'=>'')); ?>");
                        progress.setComplete(<?php echo $this->getId(); ?>deleteCallback);
                        progress.setStatus("<?php _e('Completed', 'cm-download-manager'); ?>");
                    } else {
                        //error Handling
                        progress.setStatus('<?php _e('Upload failed', 'cm-download-manager'); ?>');
                        progress.setError();
                    } 
                    progress.enableSubmit();
                                        

//                } catch (ex) {
//                    console.debug(ex.getMessage());
//                }
            }
            function <?php echo $this->getId(); ?>deleteCallback(e) {
                e.preventDefault();
                <?php echo $this->getId(); ?>checkStartValues();
                var stats = <?php echo $this->getId(); ?>swfu.getStats();
                stats.successful_uploads--;
                <?php echo $this->getId(); ?>swfu.setStats(stats);
                <?php echo $this->getId(); ?>swfu.settings.file_upload_left++;
                var cancelButton = jQuery(e.target);
                var name = cancelButton.data('name');
                var currentFiles = jQuery.parseJSON(jQuery("#<?php echo $this->getId(); ?>").val());
                var toRemove = -1;
                for (var i=0; i<currentFiles.length; i++ ) {
                    if (currentFiles[i]==name) {
                        toRemove = i;
                    }
                }
                if (toRemove>=0) {
                    currentFiles.splice(toRemove, 1);
                }
                jQuery("#<?php echo $this->getId(); ?>").val(JSON.stringify(currentFiles));
                cancelButton.parents('.progressWrapper').fadeOut('fast');
            }
                            
        </script>
        <?php
    }

    public function render() {
        $value = $this->getValue();
        if (empty($value))
            $value = array();
        if (!is_array($value))
            $value = json_decode(stripslashes($value));
        $html = '<div '.$this->_getClassName().'>';
        $html .= '<input type="hidden" id="' . $this->getId() . '" name="' . $this->getId() . '" value="' . esc_attr(json_encode($value)) . '" />';
        $html .= '<div id="' . $this->getId() . '_error" style="display:none"></div>';
        $html .= '<span id="' . $this->getId() . '_button">'.__('Upload', 'cm-download-manager').'</span>';
        $html .= '<div class="fieldset flash" id="' . $this->getId() . 'fsUploadProgress">';
        foreach ($value as $row) {
            ob_start();
            ?>
            <div class="progressWrapper">
                
                <div class="progressName" style="display:none"><?php echo $row; ?></div>
                <div class="progressImg" style="display:block"><a class="progressCancel" data-name="<?php echo $row; ?>" href="#" onclick="<?php echo $this->getId(); ?>deleteCallback(event)">x</a><img src="<?php echo CMDM_get_url('cmdownload', 'screenshot', array('size' => '196x60', 'img' => $row)); ?>" height="60" /></div>
            </div>
            <?php
            $html .= ob_get_contents();
            ob_end_clean();
        }
        $html .='</div></div>';
        $html .= $this->renderScript();
        return $html;
    }

}
?>
