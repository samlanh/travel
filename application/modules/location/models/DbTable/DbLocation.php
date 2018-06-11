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
    
	 function getAllLocationName($search=NULL){
		   	$db = $this->getAdapter();
		   	$db->beginTransaction();
		   	try{
// 		   		$from_date =(empty($search['start']))? '1': " e.`createDate` >= '".date("Y-m-d",strtotime($search['start']))." 00:00:00'";
// 		   		$to_date = (empty($search['end']))? '1': " e.`createDate` <= '".date("Y-m-d",strtotime($search['end']))." 23:59:59'";
		   		 
		   		$sql=" SELECT l.id,
				       l.serviceId,
				       l.locationName,
				       l.countryId,
				       l.status
				       FROM `tp_locations` AS l 
				       WHERE l.`status`=1
		   		";
		   		$where="";
// 		   		$where.= " AND  ".$from_date." AND ".$to_date;
// 		   		if(!empty($search['adv_search'])){
// 		   			$s_where = array();
// 		   			$s_search = addslashes(trim($search['adv_search']));
// 		   			$s_where[] = " e.employeeCode LIKE '%{$s_search}%'";
// 		   			$s_where[] = " e.sureName LIKE '%{$s_search}%'";
// 		   			$s_where[] = " e.firstName LIKE '%{$s_search}%'";
// 		   			$s_where[] = " e.latinName LIKE '%{$s_search}%'";
// 		   			$s_where[] = " e.phoneNumber LIKE '%{$s_search}%'";
// 		   			$where .=' AND ('.implode(' OR ',$s_where).')';
// 		   		}
// 		   		if($search['status']>-1){
// 		   			$where.=" AND e.status=".$search['status'];
// 		   		}
// 		   		if(!empty($search['gender'])){
// 		   			$where.=" AND e.gender= '".$search['gender']."'";
// 		   		}
		   		$order=" ORDER BY l.`locationName` ASC";
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
	
	function getAllProvince(){
		$lang= $this->getCurrentLang();
		$array = array(1=>"province_en_name",2=>"province_kh_name");
		$db = $this->getAdapter();
		$sql=" SELECT p.province_id AS id,$array[$lang] As name  FROM `rms_province` AS p WHERE p.status=1";
		return $db->fetchAll($sql);
	}
	
	function getAllService(){
		$lang= $this->getCurrentLang();
		$array = array(1=>"province_en_name",2=>"province_kh_name");
		$db = $this->getAdapter();
		$sql="  SELECT id,`serviceName` AS `name` FROM `tp_location_service` WHERE `status`=1";
		return $db->fetchAll($sql);
	}
	
}

