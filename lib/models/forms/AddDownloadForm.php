<?php

include_once CMDM_PATH . '/lib/helpers/Form.php';

class CMDM_AddDownloadForm extends CMDM_Form {

    public function init($params = array()) {
        if (isset($params['edit_id'])) {
            $editId = $params['edit_id'];
            $this->addElement(
                    CMDM_Form_Element::factory('hidden', 'edit_id')
                            ->setValue($editId)
            );
        }

        $this->setId('CMDM_AddDownloadForm')
                ->setEnctype('multipart/form-data')
                ->addElement(
                        CMDM_Form_Element::factory('text', 'title')
                        ->setLabel(__('Title', 'cm-download-manager'))
                        ->setRequired()
                )
                ->addElement(
                        CMDM_Form_Element::factory('text', 'version')
                        ->setLabel(__('Version', 'cm-download-manager'))
                        ->setRequired()
                )
                ->addElement(
                        CMDM_Form_Element::factory('fileUploader', 'package')
                        ->setLabel(__('File', 'cm-download-manager'))
                        ->setDescription('('.__('Allowed extensions', 'cm-download-manager').': '.implode(', ', get_option(CMDM_GroupDownloadPage::ALLOWED_EXTENSIONS_OPTION, array('zip', 'doc', 'docx', 'pdf'))).')')
                        ->addValidator('fileExtension', get_option(CMDM_GroupDownloadPage::ALLOWED_EXTENSIONS_OPTION, array('zip', 'doc', 'docx', 'pdf')))
                        ->setRequired()
                )
                ->addElement(CMDM_Form_Element::factory('multiCheckbox', 'categories')
                        ->setLabel(__('Category (max. 3)', 'cm-download-manager'))
                        ->setRequired()
                        ->setOptions(CMDM_GroupDownloadPage::getMainCategories())
                )
                ->addElement(
                        CMDM_Form_Element::factory('visual', 'description')
                        ->setLabel(__('Description', 'cm-download-manager'))
                        ->setSize(5, 100)
                        ->setRequired()
                )
                ->addElement(
                        CMDM_Form_Element::factory('SWFUploader', 'screenshots')
                        ->setLabel(__('Screenshots', 'cm-download-manager'))
                        ->setDescription(sprintf(__('(Max. %d, Size: H: %dpx W: %dpx)','cm-download-manager'), 4, 220, 720))
//                        ->setRequired()
                        ->setAttribs(array(
                            'uploadUrl' => home_url('/cmdownload/screenshots'),
                            'fileSizeLimit' => '1 MB',
                            'fileTypes' => '*.jpg;*.gif;*.png',
                            'fileTypesDescription' => __('Images', 'cm-download-manager'),
                            'fileUploadLimit' => 4
                        ))
        );
        if (current_user_can('manage_options')) {
            $this->addElement(
                    CMDM_Form_Element::factory('checkbox', 'admin_supported')
                            ->setLabel(__('Admin Recommended', 'cm-download-manager'))
            );
        }
        $this->addElement(
                CMDM_Form_Element::factory('checkbox', 'support_notifications')
                        ->setLabel(__('Notify me on new support topics', 'cm-download-manager'))
        );
        $this->addElement(
                CMDM_Form_Element::factory('submit', 'submit')
                        ->setValue(isset($editId) ? __('Update', 'cm-download-manager') : __('Add', 'cm-download-manager'))
        );
        if (isset($editId)) {
            $this->getElement('package')->setRequired(false);
        }
    }

}

?>
