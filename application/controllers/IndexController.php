<?php

class IndexController extends Zend_Controller_Action {

    public function indexAction(){
    }

    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->getHelper('Redirector')->setGotoSimple('index', 'Index');
    }
}

