<?php

class Vehicle_Model_DbTable_DbVehicletype extends Zend_Db_Table_Abstract
{

    protected $_name = 'tp_vehicletype';
    public function getUserId(){
    	$db = new Application_Model_DbTable_DbGlobal();
		return $db->getUserId();   
    } 
    
    static function getCurrentLang(){
    	$session_lang=new Zend_Session_Namespace('lang');
    	if(!empty($session_lang->lang_id)){
    		if ($session_lang->lang_id>2){
    			return 2;
    		}
    		return $session_lang->lang_id;
    	}else{
    		return 2;
    	}
    }
    
	 function getAllVicleType($search=NULL){
		   	$db = $this->getAdapter();
		   	$db->beginTransaction();
		   	try{
// 		   		$from_date =(empty($search['start']))? '1': " e.`createDate` >= '".date("Y-m-d",strtotime($search['start']))." 00:00:00'";
// 		   		$to_date = (empty($search['end']))? '1': " e.`createDate` <= '".date("Y-m-d",strtotime($search['end']))." 23:59:59'";
		   		 
		   		$sql=" SELECT v.id,v.*,
				       (SELECT s.serviceName FROM `tp_location_service` AS s WHERE s.id=v.`serviceType`  LIMIT 1)AS service_name
				     FROM  `tp_vehicletype` AS v ";
		   		$where=" Where 1 ";
// 		   		$where.= " AND  ".$from_date." AND ".$to_date;
		   		if(!empty($search['adv_search'])){
		   			$s_where = array();
		   			$s_search = addslashes(trim($search['adv_search']));
		   			$s_where[] = " v.title LIKE '%{$s_search}%'";
		   			$s_where[] = " v.description LIKE '%{$s_search}%'";
		   			$s_where[] = " v.amountCase LIKE '%{$s_search}%'";
		   			$s_where[] = " v.amountSmallCase LIKE '%{$s_search}%'";
		   			$s_where[] = " v.amountSeat LIKE '%{$s_search}%'";
		   			$where .=' AND ('.implode(' OR ',$s_where).')';
		   		}
		   		if($search['status']>-1){
		   			$where.=" AND v.status=".$search['status'];
		   		}
		   		$order=" ORDER BY v.`id` DESC";
		   		return $db->fetchAll($sql.$where.$order);
	   		}catch(exception $e){
	   			Application_Form_FrmMessage::message("Application Error");
	   			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
	   			$db->rollBack();
	   		}
   	}
   	
    function insertVehicleType($_data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
    		$part= PUBLIC_PATH.'/images/all/';
    		if (!file_exists($part)) {
    			mkdir($part, 0777, true);
    		}
    		$image_name = "";
    		$photo="";
    		$name = $_FILES['photo']['name'];
    		if (!empty($name)){
    			$ss = explode(".", $name);
    			$image_name = "veh_".date("Y").date("m").date("d").time().".".end($ss);
    			$tmp = $_FILES['photo']['tmp_name'];
    			if(move_uploaded_file($tmp, $part.$image_name)){
    				$photo = $image_name;
    			}else{
    				$photo = "Image Upload failed";
    			}
    		}
    		$_arr=array(
    				'title'		 		=> $_data['vehicle_type'],
    				'serviceType'	 	=> $_data['service_type'],
    				'description'      	=> $_data['description'],
    				'amountCase'      	=> $_data['amountCase'],
    				'amountSmallCase'	=> $_data['amountSmallCase'],
    				'amountSeat'      	=> $_data['amount_sit'],
    				'images'      		=> $photo,
    				'createDate'	 	=> date("Y-m-d H:i:s"),
    				'modifyDate'	 	=> date("Y-m-d H:i:s"),
    				'status'         	=> 1,
    				'userId'         	=> $this->getUserId(),
    		);
    		$pro_id =$this->insert($_arr);
    		$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
    }
    
    function updateVehicleType($_data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
    		$part= PUBLIC_PATH.'/images/all/';
    		if (!file_exists($part)) {
    			mkdir($part, 0777, true);
    		}
    		$image_name = "";
    		$photo="";
    		$name = $_FILES['photo']['name'];
    		if (!empty($name)){
    			$ss = explode(".", $name);
    			$image_name = "veh_".date("Y").date("m").date("d").time().".".end($ss);
    			$tmp = $_FILES['photo']['tmp_name'];
    			if(move_uploaded_file($tmp, $part.$image_name)){
    				$photo = $image_name;
    			}else{
    				$photo = "Image Upload failed";
    			}
    		}else{
    			$photo=$_data['old_pic'];
    		}
    			
    		$_arr=array(
    				'title'		 		=> $_data['vehicle_type'],
    				'serviceType'	 	=> $_data['service_type'],
    				'description'      	=> $_data['description'],
    				'amountCase'      	=> $_data['amountCase'],
    				'amountSmallCase'	=> $_data['amountSmallCase'],
    				'amountSeat'      	=> $_data['amount_sit'],
    				'images'      		=> $photo,
    				'modifyDate'	 	=> date("Y-m-d H:i:s"),
    		        'status'         	=> $_data['status'],
    				'userId'         	=> $this->getUserId(),
    		);
    		$this->_name="tp_vehicletype";
    		$where=" id=".$_data['id'];
    		$this->update($_arr, $where);
    		$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
    }
    
	function getAllService(){
		$lang= $this->getCurrentLang();
		$array = array(1=>"province_en_name",2=>"province_kh_name");
		$db = $this->getAdapter();
		$sql="  SELECT id,`serviceName` AS `name` FROM `tp_location_service` WHERE `status`=1";
		return $db->fetchAll($sql);
	}
	
	function getVehicleTypeBydid($id){
		$db = $this->getAdapter();
		$sql="SELECT v.*  FROM  `tp_vehicletype` AS v WHERE   v.`id`=$id";
		return $db->fetchRow($sql);
	}
	
}

