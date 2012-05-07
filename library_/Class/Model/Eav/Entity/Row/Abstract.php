<?php
abstract class Class_Model_Eav_Entity_Row_Abstract extends Zend_Db_Table_Row_Abstract
{
	protected $_entityType = null;
	protected $_attributesetId = null;
	protected $_attributesetName = null;
    protected $_attributeRowset = array();
    protected $_valueRowset = array();
    
    abstract public function getEntityType();
    abstract public function getOriginalForm($backendForm);
    /**
     * @return Zend_Db_Table
     * Enter description here ...
     */
    abstract public function getValueTable();
    
    public function setAttributesetName($val)
    {
    	$tb = new Zend_Db_Table('eav_attribute_set');
    	$row = $tb->fetchRow($tb->select()->where('name = ?', $val));
    	
    	$this->_attributesetId = $row->id;
    	$this->attributesetId = $row->id;
    	$this->_attributesetName = $row->name;
    	return $row->id;
    }
    
    public function setAttributesetId($id)
    {
    	$this->attributesetId = $id;
    	$this->_attributesetId = $id;
    	return $this;
    }
    
    /**
     * @return Zend_Form
     * 
     */
    public function getForm($backendForm = false)
    {
    	$form = $this->getOriginalForm($backendForm);
    	
		if(count($this->_attributeRowset) == 0) {
			$this->loadAttributeRowset();
		}
		foreach($this->_attributeRowset as $attr) {
			$form->addElement($attr->toElement());
		}
		
		if(!is_null($this->id)) {
			foreach($this->_valueRowset as $valueRow) {
				$form->{'attribute_'.$valueRow->attributeId}->setValue($valueRow->value);
			}
		}
		return $form;
    }
    
    public function loadAttributeRowset(Array $filters = array())
    {
        $tb = Class_Base::_('Eav_Attribute');
    	$ar = $tb->fetchAll($tb->select()->where('attributesetId = ?', $this->attributesetId));
		foreach($ar as $attribute) {
			$this->_attributeRowset[$attribute->id] = $attribute;
		}
    	
        return $this;
    }
    
    public function getAttributeRowset()
    {
        if(is_null($this->_attributeRowset)) {
            $this->loadAttributeRowset();
        }
        return $this->_attributeRowset;
    }
    
    public function resetAttributeRowset()
    {
        $this->_attributeRowset = array();
        return $this;
    }
    
    public function addAttribute(Class_Model_Eav_Attribute_Row $attribute)
    {
        $this->_attributeRowset[$attribute->id] = $attribute;
        return $this;
    }
    
    public function setAttributeValue($post)
    {
        foreach($post as $name => $value) {
            if(substr($name, 0, 10) == "attribute_") {
                $attrId = substr($name, 10);
                if(array_key_exists($attrId, $this->_attributeRowset)) {
                    $this->_attributeRowset[$attrId]->setValue($value);
                }
            }
        }
        return $this;
    }
    
    public function getValueRowset()
    {
    	$tb = $this->getValueTable();
    	$this->_valueRowset = $tb->fetchAll($tb->select()->where('entityId = ?', $this->id));
    	return $this->_valueRowset;
    }
    
    protected function _insert()
    {
    	$this->attributesetId = $this->_attributesetId;
    }
    
    protected function _postInsert()
    {
        foreach($this->_attributeRowset as $attr) {
            $attr->saveValue($this);
        }
    }
    
    protected function _postUpdate()
    {
    	$valueTable = $this->getValueTable();
    	$valueTable->delete('entityId = '.$this->id);
    	//$this->removeValues();
    	foreach($this->_attributeRowset as $attr) {
            $attr->saveValue($this);
        }
    }
}