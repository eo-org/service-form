<?php
class Admin_ElementController extends Zend_Controller_Action
{
	protected function _getResource()
	{
		$elementId = $this->getRequest()->getParam('id');
		$tb = App_Factory::_('Element');
		$row = $tb->fetchRow($tb->select()->where('id = ?', $elementId));
		return $row;
	}
	
	public function indexAction()
	{
		
	}
		
	public function editAction()
	{
		$row = $this->_getResource();
		$form = $row->getEditForm();
		//如果是单选框或者是多选框就把他们的下属选项在页面上显示出来
		if(strtolower($row['elementType']) == 'select' || $row['elementType'] == 'MultiCheckbox')
		{	

			$rowoption = $row->getOptions();
			$this->view->rowoption = $rowoption;
		}
		if($this->getRequest()->isPost()) {
			if($form->isValid($this->getRequest()->getParams())) {
				$row->setFromArray($form->getValues());
				$row->save();
				/* 获取下属表单修改的内容 $sjid是指修改的内容属于哪一下级 $val是修改的内容*/
				$sjid = $this->getRequest()->getParam('sjid');

				$optionval = $this->getRequest()->getParam('val');

				if(!empty($optionval)){

					$indb = App_Factory::_('Element_Option');

					$where = "elementId = ".$sjid;

					$indb->delete($where);

					$arrbox = explode(":", $optionval);

					for($i=0;$i<count($arrbox)-1;$i++){

						$arrin = array(

								'elementId' => $sjid,

								'label' => $arrbox[$i]

						);

						$indb->insert($arrin);//保存现有的新数据

					}

				}
				/*结束*/
				$row->clearOption();
				$this->_helper->json(array('result' => 'success', 'html' => $row->toLi()));
			} else {
				$this->_helper->json(array('result' => 'fail', 'html' => $form->getMessages()));
			}
		} 
		$this->view->row = $row;
		$this->view->form = $form;
		$this->view->rowid = $row['id'];
		$this->_helper->template->actionMenu(array(
			array('label' => '保存设定', 'href' => '/admin/element/edit/id/'.$row->id, 'method' => 'updateElementSetting'),
			array('label' => '删除', 'href' => '/admin/element/delete/id/'.$row->id, 'method' => 'delElementSetting')
		));
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
		$row = $this->_getResource();
		$id = $row->delOption();

 		$this->_helper->json(array('result' => 'success', 'html' => $id));
		exit;
	}
}