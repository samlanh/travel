<?php

class Location_Model_DbTable_DbLocation extends Zend_Db_Table_Abstract
{

    protected $_name = 'tp_locations';
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
    
	 function getAllLocationName($search=null){
		   	$db = $this->getAdapter();
		   	$db->beginTransaction();
		   	try{
// 		   		$from_date =(empty($search['start']))? '1': " e.`createDate` >= '".date("Y-m-d",strtotime($search['start']))." 00:00:00'";
// 		   		$to_date = (empty($search['end']))? '1': " e.`createDate` <= '".date("Y-m-d",strtotime($search['end']))." 23:59:59'";
		   		$sql="SELECT l.id,(SELECT ls.serviceName FROM `tp_location_service` AS ls WHERE ls.id=l.serviceId LIMIT 1) AS service_name,l.locationName,
					      (SELECT c.countryName FROM `tp_country` AS c WHERE c.id=l.countryId LIMIT 1) AS country_name,orderRing,
					      (SELECT v.name_en FROM `tp_view` AS v WHERE v.key_code=l.status AND v.type=1 LIMIT 1) AS status_name
						FROM `tp_locations` AS l 
						WHERE l.`status`=1 ";
		   		$where="";
// 		   		$where.= " AND  ".$from_date." AND ".$to_date;
		   		if(!empty($search['adv_search'])){
		   			$s_where = array();
		   			$s_search = addslashes(trim($search['adv_search']));
		   			$s_search = str_replace(' ', '', $s_search);
		   			$s_where[]="REPLACE(l.locationName,' ','')   LIKE '%{$s_search}%'";
		   			$where .=' AND ('.implode(' OR ',$s_where).')';
		   		}
		   		if($search['status']>-1){
		   			$where.=" AND status=".$search['status'];
		   		}
		   		if(!empty($search['service_type'])){
		   		    $where.=" AND l.serviceId=".$search['service_type'];
		   		}
		   		$order=" ORDER BY l.locationName ASC";
		   		return $db->fetchAll($sql.$where.$order);
	   		}catch(exception $e){
	   			Application_Form_FrmMessage::message("Application Error");
	   			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
	   			$db->rollBack();
	   		}
   	}
   	
    function insertLocation($_data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
//     		$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
//     		$part= PUBLIC_PATH.'/images/all/';
//     		if (!file_exists($part)) {
//     			mkdir($part, 0777, true);
//     		}
//     		$image_name = "";
//     		$photo="";
//     		$name = $_FILES['photo']['name'];
//     		if (!empty($name)){
//     			$ss = explode(".", $name);
//     			$image_name = "employee_".$_data['employeeCode'].date("Y").date("m").date("d").time().".".end($ss);
//     			$tmp = $_FILES['photo']['tmp_name'];
//     			if(move_uploaded_file($tmp, $part.$image_name)){
//     				$photo = $image_name;
//     			}
//     			else
//     				$string = "Image Upload failed";
//     		}
    		$_arr=array(
    				'serviceId'		 => $_data['service_type'],
    				'locationName'	 => $_data['location_name'],
    				'countryId'      => $_data['country_name'],
    				'orderRing'      => $_data['order_by'],
    				'createDate'	 => date("Y-m-d H:i:s"),
    				'status'         => 1,
    				'userId'         => $this->getUserId(),
    		);
    		$this->_name="tp_locations";
    		$pro_id =$this->insert($_arr);
    		$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
    }
    
    function updateLocation($_data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		//     		$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
    		//     		$part= PUBLIC_PATH.'/images/all/';
    		//     		if (!file_exists($part)) {
    		//     			mkdir($part, 0777, true);
    		//     		}
    		//     		$image_name = "";
    		//     		$photo="";
    		//     		$name = $_FILES['photo']['name'];
    		//     		if (!empty($name)){
    		//     			$ss = explode(".", $name);
    		//     			$image_name = "employee_".$_data['employeeCode'].date("Y").date("m").date("d").time().".".end($ss);
    		//     			$tmp = $_FILES['photo']['tmp_name'];
    		//     			if(move_uploaded_file($tmp, $part.$image_name)){
    		//     				$photo = $image_name;
    		//     			}
    		//     			else
    			//     				$string = "Image Upload failed";
    			//     		}
    		$_arr=array(
    				'serviceId'		 => $_data['service_type'],
    				'locationName'	 => $_data['location_name'],
    				'countryId'      => $_data['country_name'],
    				'orderRing'      => $_data['order_by'],
    				'createDate'	 => date("Y-m-d H:i:s"),
    				'status'         => 1,
    				'userId'         => $this->getUserId(),
    		);
    		$this->_name="tp_locations";
    		$where=" id=".$_data['id'];
    		$pro_id =$this->update($_arr, $where);
    		$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
    }
    
	function getAllLocationById($id){
		$db = $this->getAdapter();
		$sql="SELECT l.*
		       FROM `tp_locations` AS l  
		       WHERE l.`status`=1  AND l.`id`=$id";
		return $db->fetchRow($sql);
	}
 
	function getEmployeeByCode($empoyeeid){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$sql="SELECT d.*,
			(SELECT p.title FROM `tbl_position` AS p WHERE p.id = d.`position` LIMIT 1) AS positionTitle
			FROM `tbl_employee` AS d WHERE d.status=1 ";
			$where=" AND d.`employeeCode` = '$empoyeeid'";
			$order="";
			$limit=" LIMIT 1";
			return $db->fetchRow($sql.$where.$order.$limit);
		}catch(exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
	
	
	
	function getAllService(){
		$lang= $this->getCurrentLang();
		$db = $this->getAdapter();
		$sql="  SELECT id,`serviceName` AS `name` FROM `tp_location_service` WHERE `status`=1";
		$order=" ORDER BY id DESC";
		return $db->fetchAll($sql.$order);
	}
	
}

