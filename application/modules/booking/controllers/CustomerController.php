<?php
class Booking_CustomerController extends Zend_Controller_Action {
	
	const REDIRECT_URL = '/booking/customer';
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
					'isVerify'=>'',
			);
		}
		
		$db = new Booking_Model_DbTable_DbCustomer();
		$rs_rows = $db->getAllEmployee($search);
		
		$list = new Application_Form_Frmtable();
		$collumns = array("Customer","Sex","Phone","Email","Register By","Is Verify","Create Date","Status","Change");
		$link = array(
				'module'=>'booking','controller'=>'customer','action'=>'edit',
		);
		$link1=array(
				'module'=>'booking','controller'=>'customer','action'=>'changepassword',
		);
		$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('customerName'=>$link,'gender'=>$link,'tel'=>$link,'email'=>$link,'website'=>$link,'Password'=>$link1));
		
		$this->view->search = $search;
		
		
	}
	public function addAction()
	{
		$db = new Booking_Model_DbTable_DbCustomer();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			try{
				$id= $db->addSupplyer($data);
				Application_Form_FrmMessage::Sucessfull("Insert Success",self::REDIRECT_URL);
// 				$this->_redirect(self::REDIRECT_URL);
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$this->view->mainSupplyer = $db->getAllMainSupplyer();
	}
	public function editAction(){
		$id = $this->getRequest()->getParam("id");
		$db = new Booking_Model_DbTable_DbCustomer();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			try{
				$db->editSupplyer($data,$id);
				Application_Form_FrmMessage::Sucessfull("Edit Success",self::REDIRECT_URL);
// 				$this->_redirect(self::REDIRECT_URL);
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		
		$customer = $db->getCustomerById($id);
		$this->view->customer =$customer;
		//print_r($supplyer);exit();
		
	}
	
	function changepasswordAction(){
		$id = $this->getRequest()->getParam('id');
		$db = new Booking_Model_DbTable_DbCustomer();
		$row = $db->getCustomerById($id);
		
		if ($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			if($row['password'] == md5($data['oldPassword'])){
				try {
					$db->changePassword($data,$id);
					Application_Form_FrmMessage::Sucessfull('ការផ្លាស់ប្តូរដោយជោគជ័យ', self::REDIRECT_URL);
				} catch (Exception $e) {
					Application_Form_FrmMessage::message('ការផ្លាស់ប្តូរត្រូវបរាជ័យ');
				}
			}else{
				Application_Form_FrmMessage::message('ការផ្លាស់ប្តូរត្រូវបរាជ័យ');
			}
		}
		
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

