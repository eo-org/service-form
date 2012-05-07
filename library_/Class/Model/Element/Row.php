<?php
class Class_Model_Element_Row extends Zend_Db_Table_Row_Abstract implements Class_Linklist_Node
{
	protected $_pre = null;
	protected $_next = null;
	
	protected $_options = null;
	
	public function getId()
	{
		return $this->id;
	}
	
	public function setNext(Class_Linklist_Node $node)
    {
    	$this->_next = $node;
    	return $this;
    }
	
	public function getNext()
	{
		return $this->_next;
	}
	
	public function setPre(Class_Linklist_Node $node)
	{
		$this->_pre = $node;
		return $this;
	}
	
	public function getPre()
	{
		return $this->_pre;
	}
	
	public function isMultiple()
	{
		switch($this->elementType) {
			case 'Select':
				return true;
			case 'Radio':
				return true;
			case 'MultiCheckbox':
				return true;
			case 'Multiselect':
				return true;
			default:
				return false;
		}
	}
	
	public function loadOptions()
	{
		if($this->isMultiple()) {
			if(!empty($this->id)) {
				$optionTb = Class_Base::_('Element_Option');
				$optionRowset = $optionTb->fetchAll($optionTb->select()
					->from($optionTb, array('id', 'label'))
					->where('elementId = ?', $this->id)
				);
				
				$this->_options = $optionRowset;
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
	
	public function toArray()
	{
		$tmpArr = parent::toArray();
		if(!is_null($this->_options) && !is_array($this->_options)) {
			$tmpArr['options'] = $this->_options->toArray();
		}
		return $tmpArr;
	}
	
	public function toElement($disabled = false)
	{
		$html = "";
		switch($this->elementType) {
			case 'Label':
				$html.= "<div class='form-label'>".$this->label."</div>";
				break;
			case 'Seperator':
				$html.= "<div class='form-seperator'></div>";
				break;
			case 'Checkbox':
				$options = $this->getOptions();
				foreach($options as $op) {
					$html.= "<input disabled='disabled' id='option_".$op->id."' type='checkbox' name='' value='' />";
					$html.= "<label for='option_".$op->id."'>".$op->label."</label>";
				}
				break;
			case 'MultiCheckbox':
				$options = $this->getOptions();
				foreach($options as $op) {
					$html.= "<input disabled='disabled' id='option_".$op->id."' type='checkbox' name='' value='' />";
					$html.= "<label for='option_".$op->id."'>".$op->label."</label>";
				}
				break;
				break;
			case 'Radio':
			case 'Select':
				$options = $this->getOptions();
				foreach($options as $op) {
					$html.= "<input disabled='disabled' id='option_".$op->id."' type='radio' name='' value='' />";
					$html.= "<label for='option_".$op->id."'>".$op->label."</label>";
				}
				break;
			case 'Text':
				$html.= "<input type='' disabled='disabled' />";
				break;
			case 'Textarea':
				$html.= "<textarea disabled='disabled'></textarea>";
				break;
		}
		return $html;
	}
	
	public function toLi($backend = true)
	{
		$html = "";
		if($backend) {
			$html.= "<li id='form_element_".$this->id."' class='solid drag-handle' draggable='true'>";
//			$html.= "<div class='drag-handle' draggable='true'>***</div>";
			$html.= "<div class='element-label'><a draggable='false' class='lightbox-trigger' href='/admin/element/edit/id/".$this->id."'>".$this->label."</a></div>";
			$html.= "<div class='element'>".$this->toElement()."</div>";
			if(!empty($this->desc)) {
				$html.= "<div class='element-desc'>".$this->desc."</div>";
			}
			$html.= "<div class='element-controller'>";
			$html.= "</div>";
			$html.= "</li>";
		}
		return $html;
		
	}
	
	public function getEditForm()
	{
		require_once APP_PATH."/admin/forms/Element/Edit.php";
		$form = new Form_Element_Edit();
		
		$form->populate(parent::toArray());
		return $form;
	}
}