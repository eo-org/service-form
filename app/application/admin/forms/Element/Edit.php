<?php
class Form_Element_Edit extends Zend_Form
{
    public function init()
    {
    	$this->setName('element_setting');
    	
        $this->addElement('text', 'label', array(
            'filters' => array('StringTrim'),
            'label' => '项目标题：',
            'required' => true
        ));
        $this->addElement('checkbox', 'required', array(
            'label' => '必填项：',
            'required' => false
        ));
        $this->addElement('textarea', 'desc', array(
            'filters' => array('StringTrim'),
            'label' => '项目填写说明：',
            'required' => false
        ));
    }
}