<?php

class TaskController extends Zend_Rest_Controller
{

	public function init() {
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
        $this->user = Zend_Auth::getInstance()->getStorage()->read();
	}

	public function headAction(){
	}

    public function indexAction(){
        $id = $this->_request->getParam('list_id');
        if(!$id || !isset($this->user->user_id) ) {
            return $this->_helper->getHelper('json')->sendJson(array('result' => 'error'));
        }

        $task = new Application_Model_DbTable_Task;
        $select = $task->select()->where('user_id = ?', $this->user->user_id)->where('list_id = ?', $id)->order('status DESC');
        $result = $task->fetchAll($select)->toArray();
        if(empty($result)) {
            return $this->_helper->getHelper('json')->sendJson( array('result' => 'empty'));
        }

        return $this->_helper->getHelper('json')->sendJson( array('result' => 'ok', 'data' => $result));
    }

    public function postAction() {
        $body = $this->_request->getPost('task');
        $list_id = $this->_request->getPost('list_id');

        if(!$body || !isset($this->user->user_id) || !$list_id) {
            return $this->_helper->getHelper('json')->sendJson(array('result' => 'error'));
        }

        $task = new Application_Model_DbTable_Task;
        $taskId = $task->insert( array(
            'body' => $body,
            'list_id' => $list_id,
            'user_id' => $this->user->user_id,
            'status' => 'VISIBLE'
        ));

        if($taskId > 0) {
            return $this->_helper->getHelper('json')->sendJson( array('result' => 'ok', 'taskID' => $taskId));
        }

        return $this->_helper->getHelper('json')->sendJson( array('result' => 'error') );
    }

    public function putAction() {
        $body = $this->_request->getParam('body');
        $task_id = $this->_request->getParam('task_id');
        if(!$body || !isset($this->user->user_id) || !$task_id) {
            return $this->_helper->getHelper('json')->sendJson(array('result' => 'error'));
        }

        $task = new Application_Model_DbTable_Task;
        $select = $task->select()->where('user_id = ?', $this->user->user_id)->where('task_id = ?', $task_id);
        $row = $task->fetchRow($select);
        $row->body = $body;
        $row->save();

        return $this->_helper->getHelper('json')->sendJson( array('result' => 'ok'));
    }

    public function deleteAction() {
        $task_id = $this->_request->getParam('id');
        if(!isset($this->user->user_id) || !$task_id) {
            return $this->_helper->getHelper('json')->sendJson(array('result' => 'error'));
        }

        $task = new Application_Model_DbTable_Task;
        $select = $task->select()->where('user_id = ?', $this->user->user_id)->where('task_id = ?', $task_id);
        $row = $task->fetchRow($select);
        $row->delete();

        return $this->_helper->getHelper('json')->sendJson( array('result' => 'ok'));
    }

    public function getAction() {
    }

    public function markAction() {
        $status = $this->_request->getPost('status');
        $task_id = $this->_request->getPost('task_id');
        if(!$status || !isset($this->user->user_id) || !$task_id || !in_array($status, array('TODO', 'DONE') ) ) {
            return $this->_helper->getHelper('json')->sendJson(array('result' => 'error'));
        }

        $task = new Application_Model_DbTable_Task;
        $select = $task->select()->where('user_id = ?', $this->user->user_id)->where('task_id = ?', $task_id);
        $row = $task->fetchRow($select);
        $row->status = $status;
        $row->save();

        return $this->_helper->getHelper('json')->sendJson( array('result' => 'ok'));
    }
}

?>