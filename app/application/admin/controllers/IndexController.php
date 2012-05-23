<?php
class Admin_IndexController extends Zend_Controller_Action
{
	private $formCo;
	protected function _getResource()
	{
		$formId = $this->getRequest()->getParam('id');
		$formCo = App_Factory::_m('Form');
		$formDoc = $formCo->find($formId);
		if(is_null($formDoc)) {
			$formDoc = $formCo->create();
		}
		return $formDoc;
	}

	public function init()
	{
		$this->view->useJs('admin/attributeset.js');
		$this->formCo = App_Factory::_m('Form');
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('get-element-template', 'html')
            ->initContext();
	}

	public function indexAction()
	{
		$this->_helper->template->head('表单列表');
        $hashParam = $this->getRequest()->getParam('hashParam');
        $labels = array(
			'formName' => '表单标题',
			'~contextMenu' => ''
		);
		$partialHTML = $this->view->partial('select-search-header-front.phtml', array(
			'labels' => $labels,
			'selectFields' => array(
				'id' => null,
				'desc' => null
			),
			'url' => '/'.Class_Server::getOrgCode().'/admin/form/get-form-json/',
			'actionId' => 'id',
			'click' => array(
				'action' => 'contextMenu',
				'menuItems' => array(	
					array('编辑', '/'.Class_Server::getOrgCode().'/admin/form/update/id/'),
					array('编辑下属元素', '/'.Class_Server::getOrgCode().'/admin/form/edit/id/'),
					array('查看数据', '/'.Class_Server::getOrgCode().'/admin/data/index/id/'),
					array('删除', '/'.Class_Server::getOrgCode().'/admin/index/delete/id/')
				)
			),
			'initSelectRun' => 'true',
			'hashParam' => $hashParam
		));
        $this->view->partialHTML = $partialHTML;
        $this->_helper->template->actionMenu(array('create'));
	}

	public function createAction()
	{
		$orgCode = Class_Server::getOrgCode();
		$formname = $this->getRequest()->getParam('formname');
		$returnlanguage = $this->getRequest()->getParam('returnlanguage');
		if($this->getRequest()->isPost()) {
			$formCo = App_Factory::_m('Form');
			$formDoc = $formCo->addFilter('formName', $formname)->addFilter('orgCode', $orgCode)->fetchOne();
			$val = $this->getRequest()->getParam('val');
			if(empty($formDoc)) {
				$formDoc = $formCo->create();
				$arroption = array();
				if($val == ''){
					$arroption = array(
							0 => '未处理',
							1 => '已处理',
							2 => '忽视'
						);
				}else{
					$arrbox = explode(":", $val);		
					for($i=0;$i<count($arrbox)-1;$i++){
						$arroption[] = $arrbox[$i];
					}
				}
				$formDoc->setFromArray(array('formName' => $formname,'orgCode' => $orgCode,'returnlanguage' => $returnlanguage,'deal' => $arroption));
				$formDoc->save();
				$this->_helper->redirector()->gotoSimple('index');
			} else {
				$this->view->message = '该表单名称已存在，请重新输入！';
			}
		}
	}
	
	public function deleteAction()
	{
		$formid = $this->getRequest()->getParam('id');
		$formCo = App_Factory::_m('Form');
		$row = $formCo->find($formid);
		$row->delete();
		$elementCo = App_Factory::_m('Element');
		$elementDoc = $elementCo->addFilter("formId", $formid)->fetchAll();
		foreach ($elementDoc as $num){
			$optionDoc = $elementCo->addFilter("_id", $num['_id'])->fetchOne();
			$optionDoc->delete();
		}
		$this->_helper->redirector()->gotoSimple('index');
	}

	public function getFormJsonAction()
    {
        $pageSize = 20;
        $currentPage = 1;

	    $formCo = App_Factory::_m('Form');
	    $formCo->setField(array('label'));
		
        $result = array();
        foreach($this->getRequest()->getParams() as $key => $value) {
            if(substr($key, 0 , 7) == 'filter_') {
                $field = substr($key, 7);
                switch($field) {
                     case 'label':
                    	$productCo->addFilter('label', new MongoRegex("/^".$value."/"));
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