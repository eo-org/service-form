<?php
class Admin_FormController extends Zend_Controller_Action
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
			'label' => '表单标题',
			'~contextMenu' => ''
		);
		$partialHTML = $this->view->partial('select-search-header-front.phtml', array(
			'labels' => $labels,
			'selectFields' => array(
				'id' => null,
				'desc' => null
			),
			'url' => '/admin/form/get-form-json/',
			'actionId' => 'id',
			'click' => array(
				'action' => 'contextMenu',
				'menuItems' => array(
					array('编辑', '/admin/form/edit/id/'),
					array('删除', '/admin/form/delete/id/')
				)
			),
			'initSelectRun' => 'true',
			'hashParam' => $hashParam
		));

        $this->view->partialHTML = $partialHTML;
        $this->_helper->template->actionMenu(array('create'));

	}

	public function editAction()
	{
		$formid = $this->getRequest()->getParam('id');
		$formCo = App_Factory::_m('Element');
		$formDoc = $formCo->addFilter("formId", $formid)->fetchAll();
		$this->view->formElementList = $formDoc;
		$this->view->formid = $formid;
		$this->_helper->template->actionMenu(array('save', 'delete'));
	}

	public function getElementTemplateAction()
	{
		$type = $this->getRequest()->getParam('type');
		$formid = $this->getRequest()->getParam('id');
		$formCo = App_Factory::_m('Element');
		$formDoc = $formCo->create();
		if($type == 'text' || $type == 'textarea') {
			$formDoc->setFromArray(array('formId' => $formid,'elementType'=>$type,'label'=>'标题','required'=>0,'desc'=>'标题描述'));
		} else if($type == 'button') {
			$formDoc->setFromArray(array('formId' => $formid,'elementType'=>$type,'label'=>'标题','type'=>'submit'));
		} else {
			$formDoc->setFromArray(array('formId' => $formid,'elementType'=>$type,'label'=>'标题','required'=>0,'desc'=>'标题描述','option'=>array('第一选项','第二选项','第三选项')));
		}
		$formDoc->save();
		$this->view->testid = $formDoc->getId();
		switch($type) {
			case 'text':
				$this->render('element/text');
				break;
			case 'textarea':
				$this->render('element/textarea');
				break;
			case 'select':
				$this->render('element/select');
				break;
			case 'multi-checkbox':
				$this->render('element/multi-checkbox');
				break;
			case 'button':
				$this->render('element/button');
				break;
		}
		$this->getResponse()->setHeader('result', 'success');
	}

	public function getFormJsonAction()
    {
        $pageSize = 20;
        $currentPage = 1;
        
	    $formCo = App_Factory::_m('Form');
	   // $formCo->setField(array('formName'));
		$formCo->addFilter("orgCode", Class_Server::getOrgCode());
		
        $result = array();
        foreach($this->getRequest()->getParams() as $key => $value) {
            if(substr($key, 0 , 7) == 'filter_') {
                $field = substr($key, 7);
                switch($field) {
                     case 'formName':
                    	$productCo->addFilter('formName', new MongoRegex("/^".$value."/"));
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
		exit;

    }
}