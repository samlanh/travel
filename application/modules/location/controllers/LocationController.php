<?php
class Location_LocationController extends Zend_Controller_Action {
	
	const REDIRECT_URL = '/location/location';
	const REDIRECT_URL_ADD = '/location/location/add';
public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
	}
	
	public function indexAction()
	{
    // action body
    	try {
    		$db=new Location_Model_DbTable_DbLocation();
    		$result = $db->getAllLocationName();
    		$list = new Application_Form_Frmtable();
    		$collumns = array("SERVICE_TYPE","LOCATION_NAME","COUNTRY_NAME","STATUS");
    		$link=array(
    				'module'=>'location','controller'=>'location','action'=>'edit',
    		);
    		$this->view->list=$list->getCheckList(0,$collumns, $result,array('serviceId'=>$link,'locationName'=>$link));
    		if (empty($result)){
    			$result = array('err'=>1, 'msg'=>'មិនទាន់មានទិន្នន័យនៅឡើយ!');
    		}		
    	} catch (Exception $e) {
    		$result = Application_Model_DbTable_DbGlobal::getResultWarning();
    	}
    }
    
	public function addAction()
	{
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Location_Model_DbTable_DbLocation();
			try{
				$id= $db->insertLocation($data);
				if(isset($data['save_close'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL);
				} 
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL_ADD);
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$db_global=new Application_Model_DbTable_DbGlobal();
		$rs=$this->view->province=$db_global->getAllProvince();
		$this->view->service_type=$db_global->getAllService();
		 
	}
	
	public function editAction(){
		$id=$this->getRequest()->getParam('id');
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Location_Model_DbTable_DbLocation();
			try{
				$id= $db->insertLocation($data);
				if(isset($data['save_close'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL);
				}
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL_ADD);
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$db_global=new Application_Model_DbTable_DbGlobal();
		$rs=$this->view->province=$db_global->getAllProvince();
		$this->view->service_type=$db_global->getAllService();
		$this->view->row=$db->getAllLocationById($id);
	}
	
	function updateAction(){
		$blocked = $this->getRequest()->getParam('blocked');
		$blocked = (empty($blocked))? 0 : $blocked;
		
		$us_id = $this->getRequest()->getParam('id');
		$us_id = (empty($us_id))? 0 : $us_id;
		
		$db_user = new Location_Model_DbTable_DbLocation();
		$db = $db_user->updateStatus($us_id,$blocked);
		$this->_redirect(self::REDIRECT_URL);
	}
}

