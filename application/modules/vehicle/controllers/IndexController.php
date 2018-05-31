<?php
class Vehicle_IndexController extends Zend_Controller_Action {
	
	
public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
	}
	public function indexAction()
	{
		
	}
	
	public function addAction()
	{
		$db = new Vehicle_Model_DbTable_DbVehiclePrice();
		
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			try{
				$id= $db->insertPrice($data);
				if(isset($data['save_close'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS", '/vehicle/index/index');
				}else{
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS", '/vehicle/index/add');
				}
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$this->view->supplyer = $db->getAllSupplyer();
		$this->view->vehicle_type = $db->getAllVehicleType();
		$this->view->location = $db->getAllLocation();
	}
	
	public function editAction()
	{
		$id = $this->getRequest()->getParam("id");
		$db = new Vehicle_Model_DbTable_DbVehiclePrice();
	
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			try{
				$id= $db->insertPrice($data);
				Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS", '/vehicle/index/index');
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		
		$this->view->row = $db->getAllPriceById($id);
		$this->view->row_detail = $db->getAllPriceDetailById($id);
		
		$this->view->supplyer = $db->getAllSupplyer();
		$this->view->vehicle_type = $db->getAllVehicleType();
		$this->view->location = $db->getAllLocation();
	}
	
}

