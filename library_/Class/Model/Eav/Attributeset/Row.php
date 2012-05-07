<?php
class Class_Model_Eav_Attributeset_Row extends Zend_Db_Table_Row_Abstract
{
	protected $_attributeRowset;
	
	public function getAttributeRowset()
	{
		$tb = Class_Base::_('Eav_Attribute');
    	$ar = $tb->fetchAll($tb->select()->where('attributesetId = ?', $this->id));
		$this->_attributeRowset = $ar;
        return $ar;
	}
}