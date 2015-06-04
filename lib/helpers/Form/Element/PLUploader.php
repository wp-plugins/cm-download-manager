<?php
require_once CMDM_PATH . "/lib/helpers/Form/Element.php";
class CMDM_Form_Element_PLUploader extends CMDM_Form_Element
{
	
	public static $scriptsSent = array();

    public function _init()
    {
//        wp_deregister_script('plupload');
        wp_register_script('CMDM-plupload', CMDM_URL . '/views/resources/plupload/plupload.full.js', array('jquery'));
        wp_enqueue_script('CMDM-plupload');
        wp_register_script('CMDM-plupload-queue', CMDM_URL . '/views/resources/plupload/jquery.plupload.queue/jquery.plupload.queue.js', array('CMDM-plupload'));
        wp_enqueue_script('CMDM-plupload-queue');
        wp_register_style('CMDM-plupload-queue-style', CMDM_URL . '/views/resources/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css');
        wp_enqueue_style('CMDM-plupload-queue-style');
        wp_enqueue_style('json2');
    }

    public function setValue($value)
    {
        if(!empty($value) && !is_array($value))
        {
            $decodedValue = json_decode(stripslashes($value));
            if(empty($decodedValue))
            {
                parent::setValue('');
                return;
            }
        }
        parent::setValue($value);
    }

    public function isValid($value, $context = array(), $showError = false)
    {
        if(!empty($value) && !is_array($value)) $_value = json_decode(stripslashes($value));
        else $_value = $value;
        if($this->isRequired() && empty($_value))
        {
            if($showError) $this->addError(sprintf(__('%s needs to be uploaded', 'cm-download-manager'), $this->getLabel()));
            return false;
        }
        $fileUploadLimit = isset($this->_attribs['fileUploadLimit']) ? $this->_attribs['fileUploadLimit'] : 0;
        if($fileUploadLimit > 0)
        {
            if(is_array($_value) && count($_value) > $fileUploadLimit)
            {
                if($showError) $this->addError(sprintf(__e('Limit of uploaded files (%s) has been exceeded!', 'cm-download-manager'), $fileUploadLimit));
                return false;
            }
        }
        return parent::isValid($value, $context);
    }

    public function renderScript()
    {
        $uploadUrl            = $this->_attribs['uploadUrl'];
        $sizeLimit            = $this->_attribs['fileSizeLimit'];
        $fileTypes            = $this->_attribs['fileTypes'];
        $fileTypesDescription = $this->_attribs['fileTypesDescription'];
        $fileUploadLimit      = isset($this->_attribs['fileUploadLimit']) ? $this->_attribs['fileUploadLimit'] : 0;
        $fileUploadLeft       = $fileUploadLimit;
        if($fileUploadLeft > 0)
        {
            $value = $this->getValue();
            if(!is_array($value)) $value = json_decode(stripslashes($value));
            $fileUploadLeft-=count($value);
        }
        ob_start();
        ?>
        <script type="text/javascript">
            // Convert divs to queue widgets when the DOM is ready
            (function($){
                $(document).ready(function(){
                    // Custom example logic
                    $(function(){
                        var fileInput = $(<?php echo json_encode('#' . $this->getId()); ?>);
                        var uploader = new plupload.Uploader({
                            // General settings
                            runtimes: 'html5,gears,silverlight,browserplus,flash',
                            url: <?php echo json_encode($uploadUrl); ?>,
                            max_file_size: <?php echo json_encode($sizeLimit); ?>,
                            browse_button: 'pickfiles',
                            container: 'container',
                            chunk_size: '1mb',
                            unique_names: true,
                            multipart: true,
                            multi_selection: true,
                            multiple_queues: true,
                            file_data_name: 'upload',
                            // Resize images on clientside if we can
                            //                            resize: {width: 720, height: 220, quality: 90},
                            // Specify what files to browse for
                            filters: [
                                {title: <?php echo json_encode($fileTypesDescription); ?>, extensions: <?php echo json_encode($fileTypes); ?>}
                            ],
                            // Flash settings
                            flash_swf_url: <?php echo json_encode(CMDM_URL . '/views/resources/plupload/plupload.flash.swf'); ?>,
                            // Silverlight settings
                            silverlight_xap_url: <?php echo json_encode(CMDM_URL . '/views/resources/plupload/plupload.silverlight.xap'); ?>
                        });
                        uploader.bind('Init', function(up, params){
                            //                            $('#filelist').html("<div>Current runtime: " + params.runtime + "</div>");
                        });
                        $('#filelist').on('click', '.progressCancel', function(e){
                            e.preventDefault();
                            var cancelButton = $(e.target);
                            var name = cancelButton.data('name');
                            var currentFiles = $.parseJSON(fileInput.val());
                            var toRemove = -1;
                            for(var i = 0; i < currentFiles.length; i++){
                                if(currentFiles[i] == name){
                                    toRemove = i;
                                }
                            }
                            if(toRemove >= 0){
                                currentFiles.splice(toRemove, 1);
                            }
                            fileInput.val(JSON.stringify(currentFiles));
                            cancelButton.parents('.progressWrapper').fadeOut('slow', function(){
                                $(this).remove();
                            });
                        });
                        uploader.bind('FilesAdded', function(up, files){
                            $.each(files, function(i, file){
                                $('#filelist').append('<div class="progressWrapper" id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' + '</div>');
                            });
                            up.refresh(); // Reposition Flash/Silverlight

                            setTimeout(function(){
                                up.start();
                            }, 500);
                        });
                        uploader.bind('UploadProgress', function(up, file){
                            $('#' + file.id + " b").html(file.percent + "%");
                        });
                        uploader.bind('Error', function(up, err){
                            $('#filelist').append("<div>Error: " + err.code +
                                    ", Message: " + err.message +
                                    (err.file ? ", File: " + err.file.name : "") +
                                    "</div>"
                                    );
                            up.refresh(); // Reposition Flash/Silverlight
                        });
                        uploader.bind('FileUploaded', function(up, file, info){
                            var jsonResponse = $.parseJSON(info.response);
                            var container = $('#' + file.id);
                            if(jsonResponse.fileName)
                            {
                                var progressImg = $('<div/>', {
                                    'class': 'progressImg',
                                    'style': 'display:none'
                                });
                                $(progressImg)
                                        .append('<i class="progressCancel" data-name="' + jsonResponse.fileName + '">&times;</i>')
                                        .append('<img src="' + jsonResponse.imgSrc + '" height="60" />').fadeIn('slow');
                                container.html(progressImg);
                                var currentFiles = $.parseJSON(fileInput.val());
                                currentFiles.push(jsonResponse.fileName);
                                fileInput.val(JSON.stringify(currentFiles));
                            }
                        });
                        uploader.init();
                    });
                });
            })(jQuery);
        </script>
        <?php
        $html = ob_get_clean();
        return $html;
    }

    public function render()
    {
        $value = $this->getValue();

        if(empty($value))
        {
            $value = array();
        }
        if(!is_array($value))
        {
            $value = json_decode(stripslashes($value));
        }

        ob_start();
        ?>
        <div id="container">
            <div id="filelist">
                <?php foreach($value as $row) : ?>
                    <div class="progressWrapper">
                        <?php $imgSrc = CMDM_get_url('cmdownload', 'screenshot', array('size' => '196x60', 'img' => $row)); ?>
                        <div class="progressImg" style="display:block">
                            <i class="progressCancel" data-name="<?php echo esc_attr($row); ?>">&times;</i>
                            <img src="<?php echo esc_attr($imgSrc); ?>" height="60" />
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="clearfix"></div>
            <input type="button" id="pickfiles" value="Select files">
            <input type="hidden" id="<?php echo esc_attr($this->getId()) ?>" name="<?php echo esc_attr($this->getId());
            	?>" value="<?php echo esc_attr(json_encode($value)) ?>" />
        </div>
        <?php
        $html = ob_get_clean();
        
        if (empty(CMDM_Form_Element_PLUploader::$scriptsSent[$this->getId()])) {
			CMDM_Form_Element_PLUploader::$scriptsSent[$this->getId()] = true;
			$script = $this->renderScript();
	        add_action('wp_footer', function() use ($script) {
	       		echo $script;
	        });
        }

        return $html;
    }
}