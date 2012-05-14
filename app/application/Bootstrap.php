<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initAutoloader()
	{
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->registerNamespace('Class_');
		$autoloader->registerNamespace('Twig_');
		$autoloader->registerNamespace('App_');
		
		Class_Server::config();
	}
	
	protected function _initMongoDb()
	{
		$mongoDb = new App_Mongo_Db_Adapter('service-file', Class_Server::getMongoServer());
		App_Mongo_Db_Collection::setDefaultAdapter($mongoDb);
	}
//	
//	protected function _initDb()
//	{
//		$db = new Zend_Db_Adapter_Pdo_Mysql(array(
//			'host' => 'localhost',
//			'username' => 'root',
//			'password' => 'root',
//			'dbname' => 'form',
//			'adapter' => 'mysqli',
//			'charset' => 'UTF8'
//		));
//		Zend_Registry::set('db', $db);
//		Zend_Db_Table::setDefaultAdapter($db);
//	}
	
	protected function _initSession()
	{
		Zend_Session::start();
	}
	
    protected function _initController()
    {
    	Zend_Controller_Action_HelperBroker::addPath(APP_PATH.'/helpers', 'Helper');
        $controller = Zend_Controller_Front::getInstance();
        $controller->setControllerDirectory(array(
            'default' => APP_PATH.'/default/controllers',
        	'admin' => APP_PATH.'/admin/controllers',
            'rest' => APP_PATH.'/rest/controllers')
        );
        
        $csu = Class_Session_User::getInstance();
		$controller->registerPlugin(new App_Plugin_BackendSsoAuth(
        	$csu,
        	App_Plugin_BackendSsoAuth::SERVICE_FORM,
        	Class_Server::API_KEY
        ));
        
        $controller->throwExceptions(true);
        Zend_Layout::startMvc();
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('template');
        
        $view = Zend_Layout::getMvcInstance()->getView();
        $view->addHelperPath(APP_PATH.'/helpers','Helper');
    }
    
    protected function _initRouter()
    {
    	$controller = Zend_Controller_Front::getInstance();
    	$router = $controller->getRouter();
    	$defaultRoute = new Zend_Controller_Router_Route(
			':orgCode/:module/:controller/:action/*',
			array(
				'orgCode'    => Class_Server::getOrgCode(),
				'module'     => 'default',
				'controller' => 'index',
				'action'     => 'index'
			),
			array('account' => '([a-z0-9]+)')
		);
		$router->addRoute('default', $defaultRoute);
        $router->addRoute('rest', new Zend_Rest_Route($controller, array(), array('rest')));
        unset($router);
    }
}