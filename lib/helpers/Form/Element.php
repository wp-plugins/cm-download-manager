<?php


require_once CMDM_PATH . "/lib/helpers/Validate/Interface.php";
require_once CMDM_PATH . "/lib/helpers/Filter/Interface.php";


class CMDM_Form_Element {

    protected $_id;
    protected $_label;
    protected $_description;
    protected $_value;
    protected $_attribs = array();
    protected $_required = false;
    protected $_validators = array();
    protected $_errors = array();
    protected $_requiredError = '%label% is required';
    protected $_filters = array();

    public function __construct($id) {
        $this->_requiredError = sprintf(__('%s is required', 'cm-download-manager'), '%label%');
        $this->setId($id);
        $this->_init();
        return $this;
    }
    protected function _init() {
        
    }
    public function setId($id) {
        $this->_id = $id;
        return $this;
    }

    public function getId() {
        return $this->_id;
    }

    public function setLabel($label) {
        $this->_label = trim($label);
        return $this;
    }

    public function getLabel() {
        return $this->_label;
    }
    public function setDescription($description) {
        $this->_description = trim($description);
        return $this;
    }

    public function getDescription() {
        return $this->_description;
    }

    public function setValue($value) {
		if(is_array($value)){
			$this->_value=array();		
			foreach($value as $index=>$val){
				$this->_value[$index] = $this->filter($val);
			}
		}
		else {
			$this->_value = $this->filter($value);
		}
        return $this;
    }

    public function getValue() {
        return $this->_value;
    }
    public function addFilter($filter, $attribs = array()) {
        if ($filter instanceof CMDM_Filter_Interface) {
            $this->_filters[] = $filter;
        } else {
            $className = 'CMDM_Filter_' . ucfirst($filter);
            $fileName = CMDM_PATH . "/lib/helpers/Filter/" . ucfirst($filter) . '.php';
            if (!file_exists($fileName)) {
                throw new Exception(__CLASS__ . '.' . __FUNCTION__ . ': Classfile for Filter "' . $className . '" could not be found');
            } else {
                include_once $fileName;
                if (!class_exists($className)) {
                    throw new Exception(__CLASS__ . '.' . __FUNCTION__ . ': Class for Filter "' . $className . '" could not be found');
                } else {
                    $this->_filters[] = new $className($attribs);
                }
            }
        }
        return $this;
    }
    public function filter($value) {
        foreach ($this->_filters as $filter) {
            $value = $filter->filter($value);
        }
        return $value;
    }
    public function isRequired($strict = false) {
        if ($strict)
            return $this->_required || $this->hasValidator('requiredIf');
        else
            return $this->_required;
    }

    protected function _getRequired() {
        if ($this->isRequired()) {
            return ' aria-required="true"';
        } else {
            return '';
        }
    }

    public function setRequired($required = true) {
        $this->_required = $required;
        return $this;
    }

    public function addValidator($validator, $attribs = null) {
        if ($validator instanceof CMDM_Validate_Interface) {
            $this->_validators[] = $validator;
        } else {
            $className = 'CMDM_Validate_' . ucfirst($validator);
            $fileName = CMDM_PATH . "/lib/helpers/Validate/" . ucfirst($validator) . '.php';
            if (!file_exists($fileName)) {
                throw new Exception(__CLASS__ . '.' . __FUNCTION__ . ': Classfile for Validator "' . $validator . '" could not be found');
            } else {
                include_once $fileName;
                if (!class_exists($className)) {
                    throw new Exception(__CLASS__ . '.' . __FUNCTION__ . ': Class for Validator "' . $validator . '" could not be found');
                } else {
                    $this->_validators[] = new $className($attribs);
                }
            }
        }
        return $this;
    }

    public function setValidators(array $validators) {
        $this->_validators = $validators;
        return $this;
    }

    public function getErrors() {
        return $this->_errors;
    }

    protected function _renderError($error) {
        $error = str_replace('%label%', $this->getLabel(), $error);
        $value = $this->getValue();
        if (is_scalar($value)) {
        	$error = str_replace('%value%', $value, $error);
        }
        return $error;
    }

    public function addErrorClass($name = 'error') {
        $this->_attribs['class'] = trim(implode(' ', array_merge(explode(' ', (isset($this->_attribs['class']) ? $this->_attribs['class'] : '')), array($name))));
    }

    public function setErrors(array $errors) {
        foreach ($errors as $key => $val) {
            $errors[$key] = $this->_renderError($val);
        }
        $this->_errors = $errors;
        $this->addErrorClass();
        return $this;
    }
    public function setRequiredError($message) {
        $this->_requiredError = $message;
        return $this;
    }
    public function addError($error) {
        $error = $this->_renderError($error);
        $this->_errors[] = $error;
        $this->addErrorClass();
        return $this;
    }

    public function addErrors(array $errors) {
        foreach ($errors as $key => $val) {
            $errors[$key] = $this->_renderError($val);
        }
        $this->_errors = array_merge($this->_errors, $errors);
        $this->addErrorClass();
        return $this;
    }

    public function setAttribs(array $attribs) {
        $this->_attribs = $attribs;
        return $this;
    }

    public function getAttribs() {
        return $this->_attribs;
    }

    public static function factory($name, $id) {
        $className = CMDM_PREFIX.'Form_Element_' . ucfirst($name);
        if (!file_exists(CMDM_FORM_PATH . 'Element/' . ucfirst($name) . '.php')) {
            throw new Exception(__CLASS__ . '.' . __FUNCTION__ . ': Classfile for Form Element "' . $name . '" could not be found');
        } else {
            include_once CMDM_FORM_PATH . 'Element/' . ucfirst($name) . '.php';
            if (!class_exists($className)) {
                throw new Exception(__CLASS__ . '.' . __FUNCTION__ . ': Class for Form Element "' . $name . '" could not be found');
            } else {
                $elem = new $className($id);
                return $elem;
            }
        }
    }

    protected function _getClassName() {
        if (!empty($this->_attribs['class'])) {
            return ' class="' . $this->_attribs['class'] . '"';
        } else {
            return '';
        }
    }

    public function getClassName() {
        return isset($this->_attribs['class']) ? $this->_attribs['class'] : '';
    }

    public function setClassName($className) {
        $this->_attribs['class'] = $className;
        return $this;
    }

    protected function _getStyle() {
        if (!empty($this->_attribs['style'])) {
            return ' style="' . $this->_attribs['style'] . '"';
        } else {
            return '';
        }
    }

    public function getStyle() {
        return isset($this->_attribs['style']) ? $this->_attribs['style'] : '';
    }

    public function setStyle($style) {
        $this->_attribs['style'] = $style;
        return $this;
    }

    protected function _getReadonly() {
        if (isset($this->_attribs['readonly']) && $this->_attribs['readonly'] === true) {
            return ' readonly="readonly"';
        } else {
            return '';
        }
    }

    public function getReadonly() {
        return isset($this->_attribs['readonly']) ? $this->_attribs['readonly'] : false;
    }

    public function setReadonly($readonly = true) {
        $this->_attribs['readonly'] = $readonly;
        return $this;
    }

    protected function _getDisabled() {
        if (isset($this->_attribs['disabled']) && $this->_attribs['disabled'] === true) {
            return ' disabled="disabled"';
        } else {
            return '';
        }
    }

    public function getDisabled() {
        return isset($this->_attribs['disabled']) ? $this->_attribs['disabled'] : false;
    }

    public function setDisabled($disabled = true) {
        $this->_attribs['disabled'] = $disabled;
        return $this;
    }

    protected function _getOnClick() {
        if (!empty($this->_attribs['onClick'])) {
            return ' onclick="' . $this->_attribs['onClick'] . '"';
        } else {
            return '';
        }
    }

    public function getOnClick() {
        return isset($this->_attribs['onClick']) ? $this->_attribs['onClick'] : '';
    }

    public function setOnClick($onclick) {
        $this->_attribs['onClick'] = $onclick;
        return $this;
    }
    public function getValidator($name) {
        $className = 'CMDM_Validate_' . ucfirst($name);
        $val = null;
        foreach ($this->getValidators() as $validator) {
            if ($validator instanceof $className) {
                $val = $validator;
            }
        }
        return $val;
    }
    public function getValidators() {
        return $this->_validators;
    }

    public function hasValidator($name) {
        $className = 'CMDM_Validate_' . ucfirst($name);
        $has = false;
        foreach ($this->getValidators() as $validator) {
            if ($validator instanceof $className) {
                $has = true;
            }
        }
        return $has;
    }

    public function isValid($value, $context = null, $showError = false) {
        $req = $this->isRequired();
		
        if ($this->isRequired() && empty($value) && strlen($value)==0) {
            if ($showError) $this->addError($this->_requiredError);
            return false;
        } else if (!empty($value) || strlen($value)>0) {
            $valid = true;
            foreach ($this->getValidators() as $validator) {
                if (!$validator->isValid($value, $context)) {
                    $valid = false;
                    if ($showError) $this->addErrors($validator->getErrors());
                }
            }
            return $valid;
        } else {
            if ($this->hasValidator('requiredIf')) {
                $validator = $this->getValidator('requiredIf');
                if (!$validator->isValid($value, $context)) {
                    if ($showError) $this->addErrors($validator->getErrors());
                    return false;
                }
            }
            if ($this->hasValidator('requiredIfNot')) {
                $validator = $this->getValidator('requiredIfNot');
                if (!$validator->isValid($value, $context)) {
                    if ($showError) $this->addErrors($validator->getErrors());
                    return false;
                }
            }
        }
        return true;
    }

    public function setPlaceHolder($placeHolder) {
        $this->_attribs['placeHolder'] = $placeHolder;
        return $this;
    }

    protected function _getPlaceHolder() {
        if (!empty($this->_attribs['placeHolder'])) {
            return 'placeholder="' . $this->_attribs['placeHolder'] . '"';
        } else
            return '';
    }

    public function getPlaceHolder() {
        return isset($this->_attribs['placeHolder']) ? $this->_attribs['placeHolder'] : '';
    }

    public function __toString() {
        $required = ($this->isRequired())?'<span class="required">*</span>':'';
        $label = $this->getLabel();
        $description = $this->getDescription();
        $description = empty($description) ? '' : '<br/><p class="field_descr" >'.$description.'</p>';
        $label = empty($label)?'':'<label for="'.$this->getId().'" class="CMDM-form-label">'.$this->getLabel().$required.$description.'</label>';
        return '<tr><td>'.$label.'</td><td>'.$this->render().'</td></tr>';
    }
}

?>
