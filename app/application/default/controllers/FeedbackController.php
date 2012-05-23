<?php
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
		//$arrin['deal'] = '未处理';
		$formDoc = $formCo->create();
		$formDoc->setFromArray($arrin);
		$formDoc->save();
		
		$formCo = App_Factory::_m('Form');
		$formDoc = $formCo->find($arrin['formId']);
		$returnlanguage = $formDoc->returnlanguage;
		
		$this->_helper->viewRenderer->setNoRender(true);
		echo "<script language='javascript' type='text/javascript'>";
		echo "alert('".$returnlanguage."');";
		echo "window.location.href='$http'";
		echo "</script>";
		exit;
	}
	
	public function deleteAction()
	{
		$this->getResponse()->appendBody('called from delete action');
		exit;
	}
}