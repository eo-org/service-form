<?php
class Class_Model_Eav_Attribute_Row_Abstract extends Zend_Db_Table_Row_Abstract
{
	protected $_optionTbClass = 'Eav_Attribute_Option_Tb';
	
	protected $_options = null;
	protected $_newOptions = null;
	protected $_removedOptions = null;
	protected $_value = null;
	
//	public function toElement()
//	{
//		$id = $this->id;
//		if(is_null($this->_options)) {
//			$this->loadOptions();
//		}
//        $element = null;
//        if($this->frontendModel != null) {
//            
//        } else {
//            switch($this->inputType) {
//                case 'textarea':
//                    $element = new Zend_Form_Element_Textarea('attribute_'.$id, array(
//                        'label' => $this->label,
//                        'value' => $this->_value
//                    ));
//                    break;
//                case 'text':
//                    $element = new Zend_Form_Element_Text('attribute_'.$id, array(
//                        'label' => $this->label,
//                        'value' => $this->_value
//                    ));
//                    break;
//                case 'select':
//                    $element = new Zend_Form_Element_Select('attribute_'.$id, array(
//                        'label' => $this->label,
//                        'value' => $this->_value,
//                        'multiOptions' => $this->_options
//                    ));
//                    break;
//                case 'multiSelect':
//                    $element = new Zend_Form_Element_Multiselect('attribute_'.$id, array(
//                        'label' => $this->label,
//                        'value' => $this->_value,
//                        'multiOptions' => $this->_options
//                    ));
//                    break;
//                case 'datetime':
//                    $element = new Zend_Form_Element_Text('attribute_'.$id, array(
//                        'label' => $this->label,
//                        'value' => $this->_value
//                    ));
//                    break;
//                default:
//                    $element = null;
//                    break;
//            }
//        }
//        if($this->isRequired == 1) {
//            $element->setRequired(true);
//        }
//        if($this->validator != null) {
//            $element->addValidator($this->validator);
//        }
//        if($this->description != null) {
//            $element->setDescription($this->description);
//        }
//        return $element;
//	}
//	
//	public function toEditForm()
//	{
//		$id = $this->id;
//		if(is_null($this->_options)) {
//			$this->loadOptions();
//		}
//		$form = new Zend_Form();
//        $element = null;
//		switch($this->inputType) {
//			case 'text':
//				$element = new Zend_Form_Element_Text('attribute_'.$id, array(
//					'label' => '标题',
//					'value' => $this->label
//				));
//				$form->addElement($element);
//			break;
//			case 'select':
//				$element = new Zend_Form_Element_Text('attribute_'.$id, array(
//					'label' => '标题',
//					'value' => $this->label
//				));
//				$form->addElement($element);
//				foreach($this->_options as $id => $value) {
//					$element = new Zend_Form_Element_Text('option_'.$id, array(
//						'label' => '答案',
//						'value' => $value
//					));
//					$form->addElement($element);
//				}
//			break;
//		}
//		
//		return $form;
//	}
	
	public function loadOptions()
	{
		if($this->inputType == 'select' || $this->inputType == 'multiSelect' || $this->inputType == 'checkbox' || $this->inputType == '') {
			if(!empty($this->id)) {
				$optionTb = Class_Base::_($this->_optionTbClass);
				$optionRowset = 
				
				$this->_options = $opArr;
			} else {
				$this->_options = array();
			}
		}
		return $this;
	}
	
	public function getOptions()
	{
		if(is_null($this->_options)) {
			$this->loadOptions();
		}
		return $this->_options;
	}
	
	public function updateOptions(Array $options)
	{
		foreach($this->_options as $key => $value) {
			if(!array_key_exists($key, $options)) {
				$this->_removedOptions[$key] = true;
			}
		}
		$this->_options = $options;
		return $this;
	}
	
	public function setNewOptions(Array $newOptions)
	{
		$this->_newOptions = $newOptions;
		return $this;
	}
	
	public function getNewOptions()
	{
		return $this->_newOptions;
	}
	
	public function setValue($val)
	{
		$this->_value = $val;
	}
	
	public function getValueAlias()
	{
		if($this->inputType == 'select' || $this->inputType == 'multiSelect') {
			return $this->_options[$this->_value];
		} else {
			return $this->_value;
		}
	}
	
	public function saveValue(Class_Model_Eav_Entity_Row_Abstract $entity)
	{
		$tb = $entity->getValueTable();
		$newRow = $tb->createRow();
		$newRow->setFromArray(array(
			'entityId' => $entity->id,
			'attributeId' => $this->id,
			'attributeAlias' => $this->label,
			'value' => $this->_value,
			'valueAlias' => $this->getValueAlias()
		));
		$newRow->save();
	}
	
//	protected function _postInsert()
//	{
//		$optionTb = Class_Base::_('Eav_Attribute_Option');
//		$i = 1;
//		
//		foreach($this->_newOptions as $k => $v) {
//			$row = $optionTb->createRow();
//			$row->attributeId = $this->id;
//			$row->value = $v;
//			$row->sort = $i++;
//			$row->save();
//		}
//	}
//	
//	protected function _postUpdate()
//	{
//		$optionTb = Class_Base::_('Eav_Attribute_Option');
//		$i = 1;
//		foreach($this->_options as $k => $v) {
//			$row = $optionTb->find($k)->current();
//			$row->value = $v;
//			$row->sort = $i++;
//			$row->save();
//		}
//		foreach($this->_removedOptions as $k => $v) {
//			$row = $optionTb->find($k)->current();
//			$row->delete();
//		}
//		foreach($this->_newOptions as $k => $v) {
//			$row = $optionTb->createRow();
//			$row->attributeId = $this->id;
//			$row->value = $v;
//			$row->sort = $i++;
//			$row->save();
//		}
//	}
}