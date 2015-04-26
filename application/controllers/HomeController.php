<?php

class HomeController extends Zend_Controller_Action
{
	public function preDispatch()
	{
		$this->_helper->layout->setLayout('app');
	}

    public function indexAction()
    {
       // var_dump( Zend_Auth::getInstance()->getStorage()->read());
    	$this->view->myLists = 5;
    }



}

?>