<?php
class Vehicle_IndexController extends Zend_Controller_Action {
	
	
public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
	}
	public function indexAction()
	{
		$db = new Vehicle_Model_DbTable_DbVehiclePrice();
		try{
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
				$search['start']=date("Y-m-d",strtotime($search['start']));
				$search['end']=date("Y-m-d",strtotime($search['end']));
			}
			else{
				$search = array(
						'adv_search' 	=> '',
						'user'			=> '',
						'vehicleType'	=> '',
						'supplyerId'	=> '',
						'start'			=> '',
						'end'			=> date('Y-m-d'));
			}
			
			
			$rs_rows= $db->getAllVehiclePrice($search);
			$list = new Application_Form_Frmtable();
			$collumns = array("Supplyer","Vehicle Type","isAvailable","Note","Date","User","Status");
			$link=array(
					'module'=>'vehicle','controller'=>'index','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('supplyerName'=>$link,'title'=>$link,'isAvailable'=>$link));
		}catch (Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
		
		$this->view->adv_search=$search;
		
		$this->view->supplyer = $db->getAllSupplyer();
		$this->view->vehicle_type = $db->getAllVehicleType();
		
// 		$data = new Accounting_Model_DbTable_DbRegister();
// 		$this->view->rows_degree=$data->getDegree();
		 
// 		$form=new Registrar_Form_FrmSearchInfor();
// 		$form->FrmSearchRegister();
// 		Application_Model_Decorator::removeAllDecorator($form);
// 		$this->view->form_search=$form;
	
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
				$db->updatePrice($data,$id);
				Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS", '/vehicle/');
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

