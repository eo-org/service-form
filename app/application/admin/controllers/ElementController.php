<?php
class Admin_ElementController extends Zend_Controller_Action
{
	public function indexAction()
	{

	}

	public function editAction()
	{
		$this->_helper->template->head('元素基础信息修改');
		$elementId = $this->getRequest()->getParam('id');
		$formCo = App_Factory::_m('Element');
		$row = $formCo->find($elementId);
		if($this->getRequest()->isPost()) {
// 			var_export($this->getRequest()->getParams());
			$label = $this->getRequest()->getParam('label');
			$required = $this->getRequest()->getParam('required');
			$desc = $this->getRequest()->getParam('desc');
			$optionval = $this->getRequest()->getParam('option');
			$proving = $this->getRequest()->getParam('proving');
			$cssname = $this->getRequest()->getParam('cssname');
			$arrproving = array();
			$arrone = explode(":", $proving);
			for($i=0;$i<count($arrone)-1;$i++){
				$arrproving[] = $arrone[$i];
			}
			if($optionval == '0'){
				$row->setFromArray(array('desc'=>$desc,'required'=>$required,'label'=>$label,'proving'=>$arrproving,'cssname'=>$cssname));
			} else {
				$arrbox = explode(":", $optionval);
				$arroption = array();
				for($i=0;$i<count($arrbox)-1;$i++){
					$arroption[] = $arrbox[$i];
				}
				$row->setFromArray(array('option'=>$arroption,'desc'=>$desc,'required'=>$required,'label'=>$label,'proving'=>$arrproving,'cssname'=>$cssname));
			}
			$row->save();
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