<?php
class Admin_ElementController extends Zend_Controller_Action
{
	protected function _getResource()
	{
		$elementId = $this->getRequest()->getParam('id');
		//echo $elementId."<br>";
		$formCo = App_Factory::_m('Form');
		$formDoc = $formCo->find($elementId);
		//Zend_Debug::dump($formDoc);exit;
		return $formDoc;
	}

	public function indexAction()
	{

	}

	public function editAction()
	{
		$elementId = $this->getRequest()->getParam('id');
		$formCo = App_Factory::_m('Element');
		$row = $formCo->find($elementId);
		if($this->getRequest()->isPost()) {
			$label = $this->getRequest()->getParam('label');
			$required = $this->getRequest()->getParam('required');
			$desc = $this->getRequest()->getParam('desc');
			$optionval = $this->getRequest()->getParam('option');
			$arroption = array();
			if($optionval == '0'){
				$row->setFromArray(array('desc'=>$desc,'required'=>$required,'label'=>$label));
			} else {

				$arrbox = explode(":", $optionval);
				$arroption = array();
				for($i=0;$i<count($arrbox)-1;$i++){
					$arroption[] = $arrbox[$i];
				}
				$row->setFromArray(array('option'=>$arroption,'desc'=>$desc,'required'=>$required,'label'=>$label));
			}
			$row->save();
			//$this->_forward('edit','index','admin',array('id'=>$formid));
			//$this->_redirect('/admin/form/edit/id/'.$formid);
		}
		$this->view->row = $row;
		$this->view->elementid = $elementId;
	}

	public function editbuttonAction()
	{
		$elementId = $this->getRequest()->getParam('id');
		$formCo = App_Factory::_m('Element');
		$row = $formCo->find($elementId);
		if($this->getRequest()->isPost()) {
			$label = $this->getRequest()->getParam('label');
			$type = $this->getRequest()->getParam('type');
			$row->setFromArray(array('type'=>$type,'label'=>$label));
			$row->save();
		}
		$this->view->row = $row;
		$this->view->elementid = $elementId;
	}
	public function intableAction($ttype,$formid)
	{
		$tb = App_Factory::_('Element');
		$arrrow = array();
		$arrtable = array(
			'formId' =>$formid,
			'elementType' => $ttype,
			'label' => '标题',
			'required' => '0',
			'desc' =>'标题描述'
		);
		$row = $tb->insert($arrtable);
		$arrrow[0] = $row;
		if($ttype == 'select' || $ttype == 'MultiCheckbox'){
			for($i=1;$i<4;$i++){
				switch($i){

					case 1:

						$strxx = '第一选项';

						break;
					case 2:
						$strxx = '第二选项';

						break;
					case 3:
						$strxx = '第三选项';

						break;
				}
				$arrbox = array(
					'elementId' => $row,
					'label' => $strxx
				);
				$tbbox = App_Factory::_('Element_option');
				$rowbox = $tbbox->insert($arrbox);
				$arrrow[$i] = $rowbox;
			}
		}
		return $arrrow;
	}

	public function deleteAction()
	{
		$elementId = $this->getRequest()->getParam('id');
		$formCo = App_Factory::_m('Element');
		$row = $formCo->find($elementId);
		$row->delete();
		exit;
	}
}