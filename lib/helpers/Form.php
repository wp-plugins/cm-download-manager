<?php

include_once CMDM_PATH . "/lib/helpers/Form/Element.php";
define('CMDM_FORM_PATH', CMDM_PATH . '/lib/helpers/Form/');


class CMDM_Form {

    protected $_id;
    protected $_action;
    protected $_method = 'POST';
    protected $_enctype = '';
    protected $_elements = array();
    protected $_lastPos = 0;
    protected static $_instances = array();

    public function __construct($params = array()) {
        $this->init($params);
        return $this;
    }

    public function init($params = array()) {
        
    }

    public function setId($id) {
        $this->_id = $id;
        return $this;
    }

    public function getId() {
        return $this->_id;
    }

    public function addElement(CMDM_Form_Element $elem, $position = 0) {
        if ($position==0) $position=++$this->_lastPos;
        $this->_elements[$elem->getId()] = array('pos'=>$position, 'elem'=>$elem->setId($this->_id . '_' . $elem->getId()));

        return $this;
    }

    public function removeElement($id) {
        unset($this->_elements[$id]);
        return $this;
    }

    public function getElements() {
        $pos = array();
        foreach ($this->_elements as $key=>$row)
            $pos[$key] = $row['pos'];
        array_multisort($pos, SORT_ASC, $this->_elements);
        return $this->_elements;
    }

    public function getElement($id) {
        return isset($this->_elements[$id]) ? $this->_elements[$id]['elem'] : null;
    }

    public function setAction($action) {
        $this->_action = $action;
        return $this;
    }

    public function getAction() {
        return $this->_action;
    }

    public function getMethod() {
        return $this->_method;
    }

    public function setMethod($method) {
        $this->_method = $method;
        return $this;
    }public function getEnctype() {
        return $this->_enctype;
    }

    public function setEnctype($enctype) {
        $this->_enctype = $enctype;
        return $this;
    }
    

    public function getErrors() {
        $errors = array();
        foreach ($this->_elements as $elem) {
            if (count($elem['elem']->getErrors()) > 0)
                $errors = array_merge($errors, $elem['elem']->getErrors());
        }
        return $errors;
    }
    public function getErrorElements() {
        $errors = array();
        foreach ($this->_elements as $elem) {
            if (count($elem['elem']->getErrors()) > 0)
                $errors[] = $elem['elem']->getId();
        }
        return array_unique($errors);
    }

    public function isValid(array $data, $showErrors = false) {
        $this->populate($data);
        $valid = true;
        foreach ($this->_elements as $key => $value) {
            $valid = $value['elem']->isValid($this->getElement($key)->getValue(), $data, $showErrors) && $valid;
        }
        return $valid;
    }

    public function getValues() {
        $tab = array();
        foreach ($this->_elements as $key => $elem) {
            $tab[$key] = $elem['elem']->getValue();
        }
        return $tab;
    }

    public function setDefaults(array $data) {
        foreach ($this->_elements as $key => $value) {
            if (isset($data[$key])) {
                $value['elem']->setValue($data[$key]);
            }
        }
        return $this;
    }

    public function populate(array $data) {
        foreach ($this->_elements as $key => $value) {
            if (isset($data[$this->_id . '_' . $key])) {
                $value['elem']->setValue($data[$this->_id . '_' . $key]);
            }
        }
        return $this;
    }
    public function __toString() {
        $html = '';
        $errors = $this->getErrors();
        if (!empty($errors)) {
            $html = '<ul class="CMDM_error">';
            foreach ($errors as $error) {
                $html .= '<li>'.$error.'</li>';
            }
            $html .= '</ul>';
        }
        $enctype = $this->getEnctype();
        if (!empty($enctype)) $enctype=' enctype="'.$enctype.'"';
        $html .= '<form method="'.$this->getMethod().'" action="'.$this->getAction().'"'.$enctype.' class="CMDM-form"><table style="border:none">';
        foreach ($this->getElements() as $element) {
            $html .= $element['elem'];
        }
        $html.= '</table></form>';
        return $html;
    }
    
    public static function getInstance($name, $params = array()) {
        if (empty($name))
            return null;
        if (isset(self::$_instances[$name.serialize($params)])) {
            return self::$_instances[$name.serialize($params)];
        }
        else {
            include_once CMDM_PATH . '/lib/models/forms/'.$name.'.php';
            $className = CMDM_PREFIX.$name;
            self::$_instances[$name.serialize($params)] = new $className($params);
            return self::$_instances[$name.serialize($params)];
        }
        return null;
    }

}

?>
