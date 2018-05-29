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
		$collumns = array("ឈ្មោះអតិថិជន","ភេទ","លេខទូរសព្ទ","អ៊ីមែល","របៀបចុះឈ្មោះ","Is Verify","CREATE_DATE","STATUS","ប្តូរ");
		$link = array(
				'module'=>'booking','controller'=>'customer','action'=>'edit',
		);
		$link1=array(
				'module'=>'booking','controller'=>'customer','action'=>'delete',
		);
		$this->view->list=$list->getCheckList(2, $collumns, $rs_rows,array('customerName'=>$link,'gender'=>$link,'tel'=>$link,'email'=>$link,'website'=>$link,'Password'=>$link1));
		
		$this->view->search = $search;
		
		
	}
	public function addAction()
	{
		$db = new Booking_Model_DbTable_DbCustomer();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			try{
				$id= $db->addSupplyer($data);
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL);
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
				Application_Form_FrmMessage::Sucessfull("UPDATE_SUCCESS",self::REDIRECT_URL);
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

