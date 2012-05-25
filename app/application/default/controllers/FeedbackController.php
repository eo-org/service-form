<?php
require CONTAINER_PATH.'/app/application/default/forms/Fucom/Proving.php';
class FeedbackController extends Zend_Controller_Action
{
	public function init()
    {
        $this->_helper->viewRenderer->setNoRender(true);
    }
	
	public function indexAction()
	{
	}
	
	public function getAction()
	{
		$id = $this->getRequest()->getParam('id');
		
		$tb = Class_Base::_('Element');
		$rowset = $tb->fetchAll($tb->select()
			->from($tb, array('id', 'elementType', 'name', 'label', 'required', 'desc'))
			->where('formId = ?', $id)
			->order('preId')
		);
		foreach($rowset as $r) {
			$r->loadOptions();
		}
		$json = Zend_Json_Encoder::encode($rowset->toArray());
		
		$callback = $this->getRequest()->getParam('callback');
		$this->getResponse()->appendBody($callback.'('.$json.')');
	}
	
	public function replyAction()
	{
		$formCo = App_Factory::_m('Content');
		$val = $this->getRequest()->getParams();
		$arrin = array();
		$http =  $_SERVER["HTTP_REFERER"];
		$pd = 0;
		$proving = new Form_Fucom_Proving();
		foreach ($val as $num => $arrone){
			if($num != 'module' && $num != 'controller' && $num != 'action' && $num != 'button' && $num != 'id'){
				$arrid = explode("_",$num);
				if(isset($arrid[1])){
					if(isset($arrid[2])){
						$arrin[$arrid[0]][] = end($arrid);
					} else {
						$arrin[$arrid[0]] = $arrid[1];
					}
				} else {
					$arrin[$arrid[0]] = $arrone;
				}
			}
		}
		$arrin['formId'] = $val['id'];
		$elementCo = App_Factory::_m('Element');
		$elementDoc = $elementCo->addFilter('formId', $arrin['formId'])->sort('sort', 1)->fetchAll();
		$isnull = 1;
		$telephone = 1;
		$email = 1;
		$label = '';
		foreach ($elementDoc as $f => $arrtwo){
			if($arrtwo['elementType'] != 'button'){
				switch($arrtwo['proving']) {
					case 0:
						break;
					case 1:
						if(!empty($arrin[$arrtwo['label']])){
							$isnull = $proving->isnull($arrin[$arrtwo['label']]);
						}else{
							$isnull = 0;
						}
						break;
					case 2:
						if(!empty($arrin[$arrtwo['label']])){
							$telephone = $proving->telephone($arrin[$arrtwo['label']]);
						}else{
							$telephone = 0;
						}
						break;
					case 3:
						if(!empty($arrin[$arrtwo['label']])){
							$email = $proving->email($arrin[$arrtwo['label']]);
						}else{
							$email = 0;
						}
						break;
				}
			}
			if( $isnull == 0 || $telephone == 0 || $email == 0){
				$label = $arrtwo['label'];
				break;
			}
		}
		if( $isnull == 1 && $telephone == 1 && $email == 1){
			$formDoc = $formCo->create();
			$formDoc->setFromArray($arrin);
			$formDoc->save();
			$formCo = App_Factory::_m('Form');
			$formDoc = $formCo->find($arrin['formId']);
			$returnlanguage = $formDoc->returnlanguage;
			$this->_helper->viewRenderer->setNoRender(true);
		}
		if($isnull == 0){
			$returnlanguage='不能为空！';
		} else if ($telephone == 0){
			$returnlanguage='格式错误！';
		} else if ($email == 0){
			$returnlanguage='格式错误！';
		}
		$this->view->label = $label;
		$this->view->returnlanguage = $label.$returnlanguage;
		$this->view->http = $http;
		$this->render('reply');
		//exit;
	}
	
	public function deleteAction()
	{
		
		$this->getResponse()->appendBody('called from delete action');
		exit;
	}
}