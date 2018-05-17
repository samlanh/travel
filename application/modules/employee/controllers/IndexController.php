<?php
class Employee_indexController extends Zend_Controller_Action {
	
	const REDIRECT_URL = '/employee/index';
public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
	}
	public function indexAction()
	{
		if($this->getRequest()->isPost()){
			$search=$this->getRequest()->getPost();
		}
		else{
			$search = array(
					'adv_search' => '',
					'start'=> '',
					'end'=>date('Y-m-d'),
					'status'=>-1,
					'gender'=>'',
			);
		}
		$this->view->search = $search;
		$db = new Employee_Model_DbTable_DbEmployee();
		$this->view->allemployee = $db->getAllEmployee($search);
	}
	public function addAction()
	{
		$db = new Employee_Model_DbTable_DbEmployee();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			try{
				$id= $db->insertEmployee($data);
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL);
// 				$this->_redirect(self::REDIRECT_URL);
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$this->view->allposition = $db->getAllPosition();
	}
	public function editAction(){
		$db = new Employee_Model_DbTable_DbEmployee();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			try{
				$db->editEmployee($data);
				Application_Form_FrmMessage::Sucessfull("UPDATE_SUCCESS",self::REDIRECT_URL);
// 				$this->_redirect(self::REDIRECT_URL);
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$id = $this->getRequest()->getParam("id");
		empty($id)?0:$id;
		$Employee = $db->getEmployeeById($id);
		if (empty($Employee)){
			Application_Form_FrmMessage::Sucessfull("Employee not found",self::REDIRECT_URL);
			$this->_redirect(self::REDIRECT_URL);
		}
		$this->view->employee =$Employee;
		$this->view->allposition = $db->getAllPosition();
	}
	function updateAction(){
		$blocked = $this->getRequest()->getParam('blocked');
		$blocked = (empty($blocked))? 0 : $blocked;
		
		$us_id = $this->getRequest()->getParam('id');
		$us_id = (empty($us_id))? 0 : $us_id;
		
		$db_user = new Employee_Model_DbTable_DbEmployee();
		$db = $db_user->updateStatus($us_id,$blocked);
		$this->_redirect(self::REDIRECT_URL);
	}
	function deleteAction(){
		$id = $this->getRequest()->getParam('id');
		$id = (empty($id))? 0 : $id;
		
		$db_user = new Employee_Model_DbTable_DbEmployee();
		$db = $db_user->DeleteEmployee($id);
		$this->_redirect(self::REDIRECT_URL);
	}
	function checkemplyeecodeAction(){ //ajax check employee code has been use or not
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Employee_Model_DbTable_DbEmployee();
			$rs = $db->getEmployeeCode($data['employeeCode']);
			print_r(Zend_Json::encode($rs));
			exit();
		}
	}
}

