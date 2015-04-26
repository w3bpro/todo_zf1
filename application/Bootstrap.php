<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initRoutes() 
    {
        $router = Zend_Controller_Front::getInstance()->getRouter();
		$router->addRoute('sign-in', new Zend_Controller_Router_Route('sign-in/', array('controller' => 'sign', 'action' => 'index') ) );
		$router->addRoute('ajax/registration', new Zend_Controller_Router_Route('ajax/registration/', array('controller' => 'sign', 'action' => 'ajaxregistration') ) );
        $router->addRoute('log-in', new Zend_Controller_Router_Route('log-in/', array('controller' => 'sign', 'action' => 'login') ) );
        $router->addRoute('ajax/login', new Zend_Controller_Router_Route('ajax/login/', array('controller' => 'sign', 'action' => 'ajaxlogin') ) );
        $router->addRoute('home', new Zend_Controller_Router_Route('home/', array('controller' => 'home', 'action' => 'index') ) );
        $router->addRoute('logout', new Zend_Controller_Router_Route('logout/', array('controller' => 'index', 'action' => 'logout') ) );
        $router->addRoute('markTask', new Zend_Controller_Router_Route('ajax/task/mark/', array('controller' => 'task', 'action' => 'mark') ) );

        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Zend_Controller_Plugin_PutHandler());
        $restRoute = new Zend_Rest_Route($front, array(), array( 'default' => array('list') ) );
        $router->addRoute('list', $restRoute);
        $restRoute = new Zend_Rest_Route($front, array(), array( 'default' => array('task') ) );
        $router->addRoute('task', $restRoute);
		//$router->addRoute('sign-in2', new Zend_Controller_Router_Route('sign-in/:abc', array('controller' => 'sign', 'action' => 'index') ) );
    }
    protected function _initAppAutoload()
    {
        $moduleLoad = new Zend_Application_Module_Autoloader(array(
           'namespace' => '',
           'basePath'   => APPLICATION_PATH
        ));
    }
   

}

