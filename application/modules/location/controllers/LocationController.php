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
		try {
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
				 
			}else{
				$search = array(
						'adv_search' => '',
						'status'=>1,
				        'service_type'=>''
				);
			}
			$this->view->search=$search;
	        $db=new Location_Model_DbTable_DbLocation();
    		$result = $db->getAllLocationName($search);
    		$list = new Application_Form_Frmtable();
    		$collumns = array("Service Type","Location Name","Country Name","Order By","Status");
    		$link=array(
    				'module'=>'location','controller'=>'location','action'=>'edit',
    		);
    		$this->view->list=$list->getCheckList(0,$collumns, $result,array('service_name'=>$link,'locationName'=>$link));
    		if (empty($result)){
    			$result = array('err'=>1, 'msg'=>'មិនទាន់មានទិន្នន័យនៅឡើយ!');
    		}		
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
		$db_global=new Location_Model_DbTable_DbLocation();
		$this->view->service_type=$db_global->getAllService();
		 
	}
	
	public function editAction(){
		$db = new Location_Model_DbTable_DbLocation();
		$id=$this->getRequest()->getParam('id');
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			try{
				$data['id']=$id;
				$id= $db->updateLocation($data);
				if(isset($data["save_close"])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL);
					$this->_redirect(self::REDIRECT_URL);
				}else {
				  Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL);
				  $this->_redirect(self::REDIRECT_URL);
				}
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL);
				$this->_redirect(self::REDIRECT_URL);
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$db_global=new Location_Model_DbTable_DbLocation();
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
	
// 	public function checkedLocationAction(){
// 	    if($this->getRequest()->isPost()){
// 	        try {
// 	            $post=$this->getRequest()->getPost();
// 	            $db = new Location_Model_DbTable_DbLocation();
// 	            $id =$db->getCheckLocationName($post['location_name']);
// 	           // $result = array('id'=>$id);
// 	            echo Zend_Json::encode($id);
// 	            exit();
// 	        }catch (Exception $e){
// 	            $result = array('err'=>$e->getMessage());
// 	            echo Zend_Json::encode($result);
// 	            exit();
// 	        }
// 	    }
// 	}
	
	function checkedLocationAction(){
	    if($this->getRequest()->isPost()){
	        $data = $this->getRequest()->getPost();
	        $db = new Location_Model_DbTable_DbLocation();
	        $row =$db->getCheckLocationName($data['location_name']);
	        print_r(Zend_Json::encode($row));
	        exit();
	    }
	}
	
}

