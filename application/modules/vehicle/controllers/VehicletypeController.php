<?php
class Vehicle_VehicletypeController extends Zend_Controller_Action {
	
	const REDIRECT_URL = '/vehicle/vehicletype';
	const REDIRECT_URL_ADD = '/vehicle/vehicletype/add';
public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
	}
	
	public function indexAction()
	{
    // action body
    	try {
    	    if($this->getRequest()->isPost()){
    	        $search=$this->getRequest()->getPost();
    	        
    	    }else{
    	        $search = array(
    	            'adv_search' => '',
    	            'service_type'=>'',
    	            'status'=>1,
    	        );
    	    }
    	    
    		$db=new Vehicle_Model_DbTable_DbVehicletype();
    		$result = $db->getAllVicleType($search);
    		$this->view->vehicle=$result;
    		$this->view->search=$search;
    	} catch (Exception $e) {
    		$result = Application_Model_DbTable_DbGlobal::getResultWarning();
    	}
    	$db_global=new Location_Model_DbTable_DbLocation();
    	$this->view->service_type=$db_global->getAllService();
    }
    
	public function addAction()
	{
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Vehicle_Model_DbTable_DbVehicletype();
			try{
				$id= $db->insertVehicleType($data);
				if(isset($data['save_close'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL);
				} 
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL_ADD);
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$db_global=new Location_Model_DbTable_DbLocation();
		$this->view->service_type=$db_global->getAllService();
		 
	}
	
	public function editAction(){
		$id=$this->getRequest()->getParam('id');
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Vehicle_Model_DbTable_DbVehicletype();
			$data['id']=$id;
			try{
				$id= $db->updateVehicleType($data);
				if(isset($data["save_close"])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL);
					$this->_redirect(self::REDIRECT_URL);
				} 
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL_ADD);
				$this->_redirect(self::REDIRECT_URL);
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$db_global=new Vehicle_Model_DbTable_DbVehicletype();
		$this->view->service_type=$db_global->getAllService();
		$this->view->row=$db_global->getVehicleTypeBydid($id);
		 
	}
	
	function updateAction(){
		$blocked = $this->getRequest()->getParam('blocked');
		$blocked = (empty($blocked))? 0 : $blocked;
		
		$us_id = $this->getRequest()->getParam('id');
		$us_id = (empty($us_id))? 0 : $us_id;
		
		$db_user = new Vehicle_Model_DbTable_DbVehicletype();
		$db = $db_user->updateStatus($us_id,$blocked);
		$this->_redirect(self::REDIRECT_URL);
	}
}

