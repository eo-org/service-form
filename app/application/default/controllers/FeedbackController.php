<?php
require CONTAINER_PATH.'/app/application/default/forms/Fucom/Proving.php';

class FeedbackController extends Zend_Controller_Action
{
	public function init()
    {
        
    }
	
	public function indexAction()
	{
	}
	
	public function getAction()
	{
	}
	public function rtestAction()
	{
		require CONTAINER_PATH.'/app/application/default/forms/Fucom/Reply.php';
		$val = $this->getRequest()->getParams();
		$form = new Form_Fucom_Reply($val['id']);
	
		foreach ($val as $num => $arrone){
			if($num != 'module' && $num != 'controller' && $num != 'action' && $num != 'button' && $num != 'id' && $num != 'orgCode' && $num != 'callback'){
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
		Zend_Debug::dump($form->isValid($arrin));
		if($form->isValid($arrin)){
			$arrvai = array();
			foreach ($arrin as $arr_id => $arrValue){
				$elementCo = App_Factory::_m('Element');
				$elementDoc = $elementCo->find($arr_id)->toArray();
				$arrvai[$elementDoc['label']] = $arrValue;
			}
		}
		echo $form;
		exit;
	}
	public function replyAction()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$val = $this->getRequest()->getParams();
		$arrin = array();
		$http =  $_SERVER["HTTP_REFERER"];
		$pd = 0;
		$proving = new Form_Fucom_Proving();
		foreach ($val as $num => $arrone){
			if($num != 'module' && $num != 'controller' && $num != 'action' && $num != 'button' && $num != 'id' && $num != 'callback'){
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
		$only = 1;
		$label = '';
		$arrlabel = array();
		$ContentCo = App_Factory::_m('Content');
		$ContentDoc = $ContentCo->create();
		$ContentDoc->formId = $arrin['formId'];
		$arrcontent = array();
		foreach ($elementDoc as $f => $arrtwo){
			if( $isnull == 0 || $telephone == 0 || $email == 0 || $only == 0){
				break;
			}
			if($arrtwo['elementType'] != 'button'){
				$ContentDoc->$f = $arrin[$f];
				$arrcontent[$arrtwo['label']] = $arrin[$f];
				foreach($arrtwo['proving'] as $m => $arrthree){
					switch($arrthree) {
						case 0:
							break;
						case 1:
							if(!empty($arrin[$f])){
								$isnull = $proving->isnull($arrin[$f]);
							}else{
								$isnull = 0;
							}
							break;
						case 2:
							if(!empty($arrin[$f])){
								$telephone = $proving->telephone($arrin[$f]);
								$arrlabel[] = $arrin[$f];
							}else{
								$telephone = 0;
							}
							break;
						case 3:
							if(!empty($arrin[$f])){
								$email = $proving->email($arrin[$f]);								
							}else{
								$email = 0;
							}
							break;
						case 4:
							if(!empty($arrin[$f])){
								$only = $proving->only($arrtwo['label'],$arrin[$f]);
							}else{
								$only = 0;
							}
							break;
					}
					if( $isnull == 0 || $telephone == 0 || $email == 0 || $only == 0){
						$label = $arrtwo['label'];
						break;
					}
				}
			}
		}
		if( $isnull == 1 && $telephone == 1 && $email == 1 && $only == 1){
			if(count($arrlabel) == count(array_unique($arrlabel))){ 
				$arrcontent['deal'] = '新加';
				$arrcontent['时间'] = date("Y-m-d H:i:s");
				$ContentDoc->deal = '新加';
				$ContentDoc->datatime = date("Y-m-d H:i:s");
				$ContentDoc->contentvalue = $arrcontent;
				$ContentDoc->save();
				
				
				$formCo = App_Factory::_m('Form');
				$formDoc = $formCo->find($arrin['formId']);
				$returnlanguage = $formDoc->returnlanguage;
				$this->_helper->viewRenderer->setNoRender(true);
			}else{
				$returnlanguage = "电话号码重复输入,请重新输入！";
			}
		}
		if($isnull == 0){
			$returnlanguage='不能为空！';
		} else if ($telephone == 0){
			$returnlanguage='格式错误！';
		} else if ($email == 0){
			$returnlanguage='格式错误！';
		} else if ($only == 0){
			$returnlanguage='数据已存在！';
		}
		$this->view->label = $label;
		$this->view->returnlanguage = $label.$returnlanguage;
		$this->view->http = $http;
		$this->render('reply');
// 		$return = array(
// 				'message' => $this->view->returnlanguage,
// 				);
// 		$return = Zend_Json::encode($return);
// 		$this->getResponse()->appendBody($callback.'('.$return.')');
// 		$this->_helper->viewRenderer->setNoRender(true);
// 		$this->_helper->layout()->disableLayout();
		//exit;
	}
	
	public function deleteAction()
	{
		
		$this->getResponse()->appendBody('called from delete action');
		exit;
	}
}