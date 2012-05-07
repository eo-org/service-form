<?php
class Rest_IndexController extends Zend_Rest_Controller
{
	public function init()
    {
        $this->_helper->viewRenderer->setNoRender(true);
    }
	
	public function indexAction()
	{
//		$tb = Class_Base::_('Element');
//		$rowset = $tb->fetchAll();
//		$json = Zend_Json_Encoder::encode($rowset->toArray());
//		$callback = $this->getRequest()->getParam('callback');
//		$this->getResponse()->appendBody($callback.'('.$json.')');

//		Zend_Debug::dump($this->getRequest()->getParams());
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
	
	public function postAction()
	{
		$this->getResponse()->appendBody('called from post action');
	}
	
	public function putAction()
	{
		$this->getResponse()->appendBody('called from put action');
	}
	
	public function deleteAction()
	{
		$this->getResponse()->appendBody('called from delete action');
	}
}