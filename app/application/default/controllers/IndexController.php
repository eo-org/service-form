<?php
class IndexController extends Zend_Controller_Action
{
	public function indexAction()
	{
//		$formid = $this->getRequest()->getParam('id');
//		$elTb = App_Factory::_('Element');
//		$elRowset = $elTb->fetchAll($elTb->select()->where('formId = ?', $formid));
//		
//		$this->view->formid = $formid;
//		$this->view->elRowset = $elRowset;
	}
	
	public function saveAction()
	{
		$formid = $this->getRequest()->getParam('id');
		$value = $this->getRequest()->getParam('value');
		$arrbox = explode(":", $value);
		$fvTb = App_Factory::_('Feedback_Value');
		$j = 0;
		for($i=0;$i<((count($arrbox)-1)/2);$i++){
			$arrin = array(
				'userId' => '1',
				'formId' => $formid,
				'elementId' => $arrbox[$j],
				'elementlabel' => $arrbox[$j+1],
			);
			$j+=2;
			$fvTb->insert($arrin);
		}
	}
	
	public function showAction()
	{
		$formid = $this->getRequest()->getParam('id');

		$elTb = App_Factory::_('Element');

		$elRowset = $elTb->fetchAll($elTb->select()->where('formId = ?', $formid));
		$fvTb = App_Factory::_('Feedback_Value');

		$fvRowset = $fvTb->fetchAll($fvTb->select()->where('formId = ?', $formid)->where('userId = ?',1));

		$this->view->formid = $formid;
		$this->view->elRowset = $elRowset;
		$this->view->fvRowset = $fvRowset;
	}
}