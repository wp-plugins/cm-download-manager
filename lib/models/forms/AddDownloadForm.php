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
                        ->setLabel('Title')
                        ->setRequired()
                )
                ->addElement(
                        CMDM_Form_Element::factory('text', 'version')
                        ->setLabel('Version')
                        ->setRequired()
                )
                ->addElement(
                        CMDM_Form_Element::factory('fileUploader', 'package')
                        ->setLabel('File')
                        ->setDescription('(Allowed extensions: '.implode(', ', get_option(CMDM_GroupDownloadPage::ALLOWED_EXTENSIONS_OPTION, array('zip', 'doc', 'docx', 'pdf'))).')')
                        ->addValidator('fileExtension', get_option(CMDM_GroupDownloadPage::ALLOWED_EXTENSIONS_OPTION, array('zip', 'doc', 'docx', 'pdf')))
                        ->setRequired()
                )
                ->addElement(CMDM_Form_Element::factory('multiCheckbox', 'categories')
                        ->setLabel('Category (max. 3)')
                        ->setRequired()
                        ->setOptions(CMDM_GroupDownloadPage::getMainCategories())
                )
                ->addElement(
                        CMDM_Form_Element::factory('visual', 'description')
                        ->setLabel('Description')
                        ->setSize(5, 100)
                        ->setRequired()
                )
                ->addElement(
                        CMDM_Form_Element::factory('SWFUploader', 'screenshots')
                        ->setLabel('Screenshots')
                        ->setDescription('(Max. 4, Size: H: 220px W: 720px)')
//                        ->setRequired()
                        ->setAttribs(array(
                            'uploadUrl' => home_url('/cmdownload/screenshots'),
                            'fileSizeLimit' => '1 MB',
                            'fileTypes' => '*.jpg;*.gif;*.png',
                            'fileTypesDescription' => 'Images',
                            'fileUploadLimit' => 4
                        ))
        );
        if (current_user_can('manage_options')) {
            $this->addElement(
                    CMDM_Form_Element::factory('checkbox', 'admin_supported')
                            ->setLabel('Admin Recommended')
            );
        }
        $this->addElement(
                CMDM_Form_Element::factory('checkbox', 'support_notifications')
                        ->setLabel('Notify me on new support topics')
        );
        $this->addElement(
                CMDM_Form_Element::factory('submit', 'submit')
                        ->setValue(isset($editId) ? 'Update' : 'Add')
        );
        if (isset($editId)) {
            $this->getElement('package')->setRequired(false);
        }
    }

}

?>
