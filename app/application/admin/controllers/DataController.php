<?php
class Admin_DataController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$this->_helper->template->head('数据列表');
		$formid = $this->getRequest()->getParam('id');
// 		$contentCo = App_Factory::_m('Element');
// 		$contentDoc = $contentCo->addFilter("formId", $formid)->fetchAll(true);
// 		$first = '';
// 		foreach ($contentDoc as $f => $num){
// 			if (array_key_exists("sort",$num)){
// 				if($num['sort'] == 1){
// 					$first = $num['label'];
// 				}
// 			} else {
// 				$first = $contentDoc[0]['label'];
// 				break;
// 			}
// 		}
		$formCo = App_Factory::_m('Form');
		$formDoc = $formCo->find($formid);
		$form = $formDoc->toArray();
		
		$arrdeal = array();
		$arrshowlist = array();
		foreach ($form['deal'] as $m => $arrone){
			$arrdeal[$arrone] = $arrone;
		}
		$arrdeal['undefined'] = '新加';
		if(empty($form['showlist'])){
			$this->_redirect(Class_Server::getOrgCode().'/admin/data/showlist/id/'.$formid);
		}
		foreach ($form['showlist'] as $n => $arrtwo){
			$arrshowlist[$arrtwo] = $arrtwo;
		}
		$arrshowlist['deal'] = '处理';
		$arrshowlist['~contextMenu'] = '';
		$hashParam = $this->getRequest()->getParam('hashParam');
        $labels = $arrshowlist;
		$partialHTML = $this->view->partial('select-search-header-front.phtml', array(
			'labels' => $labels,
			'selectFields' => array(
				'deal' => $arrdeal
			),
			'url' => '/'.Class_Server::getOrgCode().'/admin/data/get-form-json/id/'.$formid.'/',
			'actionId' => 'id',
			'click' => array(
				'action' => 'contextMenu',
				'menuItems' => array(	
					array('编辑', '/'.Class_Server::getOrgCode().'/admin/data/edit/id/'),
					array('删除', '/'.Class_Server::getOrgCode().'/admin/data/delete/formid/'.$formid.'/id/')
				)
			),
			'initSelectRun' => 'true',
			'hashParam' => $hashParam
		));
		$this->view->formid = $formid;
        $this->view->partialHTML = $partialHTML;
	}

	public function editAction()
	{
		$contentid = $this->getRequest()->getParam('id');
		$contentCo = App_Factory::_m('Content');
		$contentDoc = $contentCo->find($contentid);
		$formId = $contentDoc->formId;
		$formCo = App_Factory::_m('Form');
		$formDoc = $formCo->find($formId);
		if($this->getRequest()->isPost()) {
			$deal = $this->getRequest()->getParam('deal');
			$contentDoc->setFromArray(array('deal' => $deal));
			$contentDoc->save();
		}
		$this->view->formElementList = $contentDoc;
		$this->view->formAttr = $formDoc;
		$this->view->contentid = $contentid;
		$this->view->formid = $formId;
	}
	
	public function deleteAction()
	{
		$formid = $this->getRequest()->getParam('formid');
		$contentid = $this->getRequest()->getParam('id');
		$contentCo = App_Factory::_m('Content');
		$row = $contentCo->find($contentid);
		$row->delete();
		$this->_redirect(Class_Server::getOrgCode().'/admin/data/index/id/'.$formid);
		exit;
	}
	
	public function showlistAction()
	{
		$this->_helper->template->head('显示列表选择');
		$formid = $this->getRequest()->getParam('id');
		$contentCo = App_Factory::_m('Element');
		$contentDoc = $contentCo->addFilter("formId", $formid)->sort('sort', 1)->fetchAll(true);
		$formCo = App_Factory::_m('Form');
		$formDoc = $formCo->find($formid);
		if($this->getRequest()->isPost()) {
			$val = $this->getRequest()->getParam('val');
			$arroption = array();
			$arrbox = explode(":", $val);
			for($i=0;$i<count($arrbox)-1;$i++){
				$arroption[] = $arrbox[$i];
			}
			$formDoc->setFromArray(array('showlist' => $arroption));
			$formDoc->save();
		}
		$this->view->formid = $formid;
		$this->view->contentrow = $contentDoc;
		$this->view->formrow = $formDoc;
	}

	public function getFormJsonAction()
    {
        $pageSize = 20;
        $currentPage = 1;
        $formid = $this->getRequest()->getParam('id');
	    $formCo = App_Factory::_m('Content');
	    $formCo->addFilter("formId", $formid);
		
        $result = array();
        foreach($this->getRequest()->getParams() as $key => $value) {
            if(substr($key, 0 , 7) == 'filter_') {
                $field = substr($key, 7);
                switch($field) {
                     case 'deal':
                    	$productCo->addFilter('deal', new MongoRegex("/^".$value."/"));
                		break;
                    case 'page':
            			if(intval($value) != 0) {
            				$currentPage = $value;
            			}
                        $result['currentPage'] = intval($value);
            		    break;
                }
            }
        }

        $formCo->setPage($currentPage)->setPageSize($pageSize);
		$data = $formCo->fetchAll(true);
		$dataSize = $formCo->count();

		$result['data'] = $data;
        $result['dataSize'] = $dataSize;
        $result['pageSize'] = $pageSize;
        $result['currentPage'] = $currentPage;

        return $this->_helper->json($result);
    }

}