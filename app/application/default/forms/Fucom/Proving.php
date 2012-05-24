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
    	//$arrid = explode("-",$string);
    	//return (preg_match("/(?:13d{1}|15[03689])d{8}$/",$str))?true:false;
    	//return (preg_match("/^(((d{3}))|(d{3}-))?((0d{2,3})|0d{2,3}-)?[1-9]d{6,8}$/",$str))?true:false; 
    	if(strlen($string)<8){
    		return 0;
    	}
    	if(preg_match("/^\d*$/",$string)){
    		return 1;
    	}else{
    		return 0;
    	}
    }
    
    public function email($string)
    {
    	return preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $string); 
    }
}