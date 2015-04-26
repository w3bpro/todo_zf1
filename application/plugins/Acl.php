<?php

class Application_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {

        $acl = new Zend_Acl();   
        //roles
        $acl->addRole('guest');
        $acl->addRole('user', 'guest');

        //resources
        $acl->addResource('index');
        $acl->addResource('sign');
        $acl->addResource('home');
        $acl->addResource('list'); //rest
        $acl->addResource('task');

        //guest
        $acl->allow('guest', 'index', 'logout');    
        $acl->allow('guest', 'index', 'index');    
        $acl->allow('guest', 'sign');  
        
        //user
        $acl->allow('user', 'home'); 
        $acl->allow('user', 'list');  
        $acl->allow('guest', 'task');  

        //check is user allowed to resoruce
        $user = Zend_Auth::getInstance()->getIdentity();
        if(null === $user) {
            $role = 'guest';
        } else {
            $role = 'user';
        }
        
        if (!$acl->isAllowed($role, strtolower( $request->getControllerName() ), $request->getActionName() ) ) {
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            return $redirector->setGotoSimple('index', 'Index');
        }
    }       
}

?>