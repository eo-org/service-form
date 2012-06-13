<?php
class Form_Fucom_Proving 
{
    public function init()
    {
    }
    
    public function isnull($string)
    {
    		return ($string == '')?0:1;
    }
    
    public function telephone($string)
    {
    	$arrid = explode("-",$string);
//     	if (count($arrid)>1){
//     		return (preg_match("/^(((d{3}))|(d{3}-))?((0d{2,3})|0d{2,3}-)?[1-9]d{6,8}$/",$string))?1:0;
//     	}else{
//     		return (preg_match("/(?:13d{1}|15[03689])d{8}$/",$string))?1:0;
//     	}
		if(count($arrid)>1){
	    	if(preg_match("/^\d*$/",$arrid[0])&&preg_match("/^\d*$/",$arrid[1])){
	    		return 1;
	    	}else{
	    		return 0;
	    	}
		}else{
			if(preg_match("/^\d*$/",$string)){
				return 1;
			}else{
				return 0;
			}
		}
    }
    
    public function email($string)
    {
       $reg = preg_match("/^[0-9a-zA-Z]+(?:[\_\-][a-z0-9\-]+)*@[a-zA-Z0-9]+(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]+$/i",$string);
       echo '123'."<br/>";
       echo $reg;exit;
       return $reg;	
    }
    
    public function only($condition,$string)
    {
    	$contentCo = App_Factory::_m('Content');
    	$contentDoc = $contentCo->addFilter($condition, $string)->fetchOne();
//     	Zend_Debug::dump($contentDoc);
    	if(is_null($contentDoc)){
    		return 1;	
    	} else {
    		return 0;
    		
    	} 
    }
}