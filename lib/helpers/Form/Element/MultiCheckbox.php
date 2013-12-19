<?php

require_once CMDM_PATH . "/lib/helpers/Form/Element.php";


class CMDM_Form_Element_MultiCheckbox extends CMDM_Form_Element {

    protected $_options = array();
    protected $_removeable = true;

    public function setOptions($options) {
        $this->_options = $options;
        return $this;
    }

    public function getOptions() {
        return $this->_options;
    }

    public function setRemoveable($removeable = true) {
        $this->_removeable = $removeable;
        return $this;
    }

    public function isRemoveable() {
        return $this->_removeable;
    }

    public function render() {

        $html ='<script type="text/javascript">
 jQuery(document).ready(function ($) {
 $("input[type=checkbox][name=\''.$this->getId().'[]\']").click(function() {
 if ($("input[type=checkbox][name=\''.$this->getId().'[]\']:checked").length>3) {
 $(this).prop("checked", false);
}
});
    });
		</script>';

        $html .='<ul '.$this->_getClassName() . '>';
        $html .= $this->_renderOptions();
        $html .='</ul>';

        return $html;
    }

    protected function _renderOptions() {
        $html = '';
        foreach ($this->getOptions() as $key => $val) {
            $readonly = $this->getReadonly();
            $html .= '<li><input type="checkbox" value="' . $key . '" id="' . $this->_id . $key . '"  name="' . $this->_id
                    . '[]"';
            if (!empty($this->_value))
            if (in_array($key, $this->_value)) {
                if (!$this->isRemoveable())
                    $readonly = true;
                $html.=' checked="checked"';
            }
            $_readonly = '';
            if ($readonly) {
                $_readonly = ' readonly="readonly"';
            }
            $html .= $this->_getStyle() . $_readonly . $this->_getOnClick();
            // if ($key == $this->getValue())
            //     $html .= ' selected="selected"';
            $html .= ' /><label for="' . $this->_id . $key . '">' . $val . '</label></li>';
        }
        return $html;
    }

//       public function renderedOptions() {
//            foreach ($this->getOptions() as $key=>$val) {
//            $html .= '<input type="checkbox" value="'.$key.'" id="'.$this->getId().'" size= "'.$this->getSize().'" name="'.$this->getId()
//                .'"'.$this->_getClassName().$this->_getStyle().$this->_getReadonly().$this->_getOnClick();
//            if ($key == $this->getValue()) $html .= ' selected="selected"';
//            $html .= ' />'.$val;
//        }
//        return $html;
//    }
}

?>
