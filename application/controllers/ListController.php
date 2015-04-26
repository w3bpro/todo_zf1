<?php

class ListController extends Zend_Rest_Controller
{

	public function init() {
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
	}

	public function headAction(){
	}

    public function indexAction(){
    	$list = new Application_Model_DbTable_List;
    	$user = Zend_Auth::getInstance()->getStorage()->read();
    	$select = $list->select()->where('user_id = ?', $user->user_id);
    	$result = $list->fetchAll($select)->toArray();

    	if(empty($result)) {
    		return $this->_helper->getHelper('json')->sendJson( array('result' => 'empty'));
    	}

    	return $this->_helper->getHelper('json')->sendJson( array('result' => 'ok', 'data' => $result));
    }

    public function postAction() {
    	$name = $this->_request->getPost('name');
    	$user = Zend_Auth::getInstance()->getStorage()->read();

    	if(!$name || !isset($user->user_id)) {
			return $this->_helper->getHelper('json')->sendJson( array('result' => 'error') );
    	}

    	$list = new Application_Model_DbTable_List;
    	$listId = $list->insert( array(
    		'name' => $name,
    		'user_id' => $user->user_id
		));

		if($listId > 0) {
			return $this->_helper->getHelper('json')->sendJson( array('result' => 'ok', 'listID' => $listId));
		}

		return $this->_helper->getHelper('json')->sendJson( array('result' => 'error') );
    }

    public function putAction() {
    	echo 'put update';
    }

    public function deleteAction() {
    	echo 'delete';
    }

    public function getAction() {
    	echo 'get signle';
    }
}

?>