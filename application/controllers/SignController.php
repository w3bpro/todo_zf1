<?php

class SignController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        $form    = new Application_Form_Registration();
        //$temp_csrf = new Zend_Session_Namespace('temp_csrf'); 
        $this->view->form = $form;
       // $temp_csrf->token = $form->csrf->getHash();
    }

    public function loginAction() 
    {
        $request = $this->getRequest();
        $form    = new Application_Form_Login();
        $this->view->form = $form;
    }

    public function restorePasswordAction()
    {
        // action body
    }

    public function ajaxregistrationAction()
    {
        $temp_csrf = new Zend_Session_Namespace('temp_csrf');
        $req =  $this->getRequest();
        if(!$this->_request->isPost() ) { //|| $req->getPost('csrf') != $temp_csrf->token) {
            $this->_helper->getHelper('json')->sendJson('error');
        }
        
        $form = new Application_Form_Registration;
        $valid = $form->isValid($this->_getAllParams() );
        if(!$valid){
            $this->_helper->getHelper('json')->sendJson(array('result' => 'error', 'messages' => $form->getMessages() ));
        }
        //create user
        $user = new Application_Model_DbTable_User();
        $salt = App_Password::getInstance()->salt($req->getPost('pass'));
        $userID = $user->insert( array(
            'pass' =>  $salt->pass,
            'salt' => $salt->salt,
            'email' => $req->getPost('email'),
            'registrationDate' => new Zend_Db_Expr('NOW()')
        ));
        
        if(!$userID > 0) {
            return $this->_helper->getHelper('json')->sendJson('error');
        }


        //auto log in
        $db =  Zend_Db_Table::getDefaultAdapter();
        $adapter = new Zend_Auth_Adapter_DbTable($db);
        $adapter->setTableName('users')
                ->setIdentityColumn('email')
                ->setCredentialColumn('pass');

        $adapter->setIdentity($req->getPost('email'))
                ->setCredential($salt->pass);

        $result = Zend_Auth::getInstance()->authenticate($adapter);

        /*
        if ($result->getCode() == Zend_Auth_Result::SUCCESS) {
            // sukces
            echo 'Logowanie powiodło się.';
        } else {
            // niepoprawne dane
            echo 'Logowanie nie powiodło się - podano niepoprawny login lub hasło.';
        }

        $auth->hasIdentity()
        */

        return $this->_helper->getHelper('json')->sendJSon( array('result' => 'ok', 'user_id' => $userID ) );
    }

    public function ajaxloginAction()
    {
        $req =  $this->getRequest();
        $form = new Application_Form_Login;
        $valid = $form->isValid($this->_getAllParams() );
        if(!$valid){
           return $this->_helper->getHelper('json')->sendJson(array('result' => 'error', 'messages' => $form->getMessages() ));
        }

        $email = $req->getPost('email');
        $pass = $req->getPost('pass');
        //get user_id and password salt
        $db =  Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()->from('users',array( 'salt', 'user_id') )->where('email = ?', $email)->limit(1);
        $result = $db->fetchRow($select);
        //salt password with saved salt
        $salt = App_Password::getInstance()->saltWith($pass, $result['salt']);
        $adapter = new Zend_Auth_Adapter_DbTable($db);
        $adapter->setTableName('users')
                ->setIdentityColumn('email')
                ->setCredentialColumn('pass');

        $adapter->setIdentity($email)
                ->setCredential($salt->pass);

        Zend_Auth::getInstance()->authenticate($adapter);
        Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session);
        $authStorage = Zend_Auth::getInstance()->getStorage();  
        $userInfo = $adapter->getResultRowObject(null, array('pass', 'salt'));
        $authStorage->write( $userInfo ); 

        return $this->_helper->getHelper('json')->sendJson(array('result' => 'ok'));
    }

}



