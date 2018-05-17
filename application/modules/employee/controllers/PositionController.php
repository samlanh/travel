<?php
class Employee_positionController extends Zend_Controller_Action {
	
	const REDIRECT_URL = '/employee/position';
public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
	}
	public function indexAction()
	{
		$db = new Employee_Model_DbTable_DbPosition();
		$this->view->allposition = $db->getAllPosition();
	}
	public function addAction()
	{
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Employee_Model_DbTable_DbPosition();
			try{
				$id= $db->insertPosition($data);
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL);
// 				$this->_redirect(self::REDIRECT_URL);
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
	}
	public function editAction(){
		$db = new Employee_Model_DbTable_DbPosition();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			try{
				$db->insertPosition($data);
				Application_Form_FrmMessage::Sucessfull("UPDATE_SUCCESS",self::REDIRECT_URL);
// 				$this->_redirect(self::REDIRECT_URL);
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$id = $this->getRequest()->getParam("id");
		empty($id)?0:$id;
		$position = $db->getPositionById($id);
		if (empty($position)){
			Application_Form_FrmMessage::Sucessfull("Position not found",self::REDIRECT_URL);
			$this->_redirect(self::REDIRECT_URL);
		}
		$this->view->position =$position;
	}
	function updateAction(){
		$blocked = $this->getRequest()->getParam('blocked');
		$blocked = (empty($blocked))? 0 : $blocked;
		
		$us_id = $this->getRequest()->getParam('id');
		$us_id = (empty($us_id))? 0 : $us_id;
		
		$db_user = new Employee_Model_DbTable_DbPosition();
		$db = $db_user->updateStatus($us_id,$blocked);
		$this->_redirect(self::REDIRECT_URL);
	}
}

