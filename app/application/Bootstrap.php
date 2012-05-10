<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initAutoloader()
	{
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->registerNamespace('Class_');
		$autoloader->registerNamespace('Twig_');
		$autoloader->registerNamespace('App_');
	}
	
	protected function _initMongoDb()
	{
		$mongoDb = new App_Mongo_Db_Adapter('service-form');
		App_Mongo_Db_Collection::setDefaultAdapter($mongoDb);
	}
	
	protected function _initDb()
	{
		$db = new Zend_Db_Adapter_Pdo_Mysql(array(
			'host' => 'localhost',
			'username' => 'root',
			'password' => 'root',
			'dbname' => 'form',
			'adapter' => 'mysqli',
			'charset' => 'UTF8'
		));
		Zend_Registry::set('db', $db);
		Zend_Db_Table::setDefaultAdapter($db);
	}
	
	protected function _initSession()
	{
		Zend_Session::start();
	}
	
    protected function _initController()
    {
    	Class_Server::config();
    	
    	Zend_Controller_Action_HelperBroker::addPath(APP_PATH.'/helpers', 'Helper');
        $controller = Zend_Controller_Front::getInstance();
        $controller->setControllerDirectory(array(
            'default' => APP_PATH.'/default/controllers',
        	'admin' => APP_PATH.'/admin/controllers',
            'rest' => APP_PATH.'/rest/controllers')
        );
        
        $controller->throwExceptions(true);
        Zend_Layout::startMvc();
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('template');
//        $controller->registerPlugin(new App_Plugin_LayoutSwitch($layout, array('admin')));

        //add view helper path
        $view = Zend_Layout::getMvcInstance()->getView();
        $view->addHelperPath(APP_PATH.'/helpers','Helper');
        
    }
    
    protected function _initRouter()
    {
    	$controller = Zend_Controller_Front::getInstance();
    	$router = $controller->getRouter();
        $router->addRoute('rest', new Zend_Rest_Route($controller, array(), array('rest')));
    }
}