<?php
class Admin_View_Helper_UseJs extends Zend_View_Helper_Placeholder_Container_Standalone
{
	public function useJs($value)
	{
		$libVersion = 'v1';
		$libHost = 'lib.eo.test';
		return $this->view->headScript()->appendFile('http://'.$libHost.'/'.$libVersion.'/script/'.$value);
	}
}