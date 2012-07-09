<?php
class Form_Fucom_Reply extends Zend_Form
{
	private $_id;
	public function __construct($id)
	{
		$this->_id = $id;
		parent::__construct();
	}
	
    public function init()
    {
    	$this->setName('element_setting');
    	
    	$elementCo = App_Factory::_m('Element');
		$elementDoc = $elementCo->addFilter('formId', $this->_id)->sort('sort', 1)->fetchDoc();
    	foreach ($elementDoc as $num => $arrDoc){
    		$arrValidator = array ();
    		if(count($arrDoc->proving) != 0){
	    		foreach ($arrDoc->proving as $f => $arrVai){
	    			
	    			switch($arrVai) {
	    				case '0':
	    					break;
	    				case '1':
	    					$arrValidator[] = array('NotEmpty');
	    					break;
	    				case '2':
	    					$arrValidator[] = array('Digits');
	    					break;
	    				case '3':
	    					$arrValidator[] = array('EmailAddress');
	    					break;
	    				case '4':
	    					break;
	    				
	    			}
	    		}
    		}
    		if($arrDoc->elementType == 'select' || $arrDoc->elementType == 'multi-checkbox' || $arrDoc->elementType == 'menu'){
    			switch($arrDoc->elementType) {
    				case 'select':
    					$arrDoc->elementType = 'radio';
    					break;
    				case 'multi-checkbox':
    					$arrDoc->elementType = 'multicheckbox';
    					break;
    				case 'menu':
    					$arrDoc->elementType = 'select';
    					break;
    			}
	    		$this->addElement($arrDoc->elementType, $arrDoc->getId(), array(
	    				//'filters' => array('StringTrim'),
	    				'label' =>  $arrDoc->label,
	    				'required' => true,
	    				'multiOptions' => $arrDoc->option	    				
	    		));
    		} else if($arrDoc->elementType == 'text' || $arrDoc->elementType == 'textarea'){
    			$this->addElement($arrDoc->elementType, $arrDoc->getId(), array(
    					//'filters' => array('StringTrim'),
    					'label' =>  $arrDoc->label,
    					'validators' => $arrValidator
    			));
    		}
    	}
    }
 
}