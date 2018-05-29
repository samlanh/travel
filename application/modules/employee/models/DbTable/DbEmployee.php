<?php

class Employee_Model_DbTable_DbEmployee extends Zend_Db_Table_Abstract
{

    protected $_name = 'tbl_employee';
    public function getUserId(){
    	$db = new Application_Model_DbTable_DbGlobal();
		return $db->getUserId();   
    } 
	 function getAllEmployee($search=NULL){
		   	$db = $this->getAdapter();
		   	$db->beginTransaction();
		   	try{
		   		$from_date =(empty($search['start']))? '1': " e.`createDate` >= '".date("Y-m-d",strtotime($search['start']))." 00:00:00'";
		   		$to_date = (empty($search['end']))? '1': " e.`createDate` <= '".date("Y-m-d",strtotime($search['end']))." 23:59:59'";
		   		 
		   		$sql="
		   			SELECT e.*,
					(SELECT p.title FROM `tbl_position` AS p WHERE p.id = e.`position` LIMIT 1) AS positionTitle
					FROM `tbl_employee` AS e 
					WHERE e.`status`>-1
		   		";
		   		$where="";
		   		$where.= " AND  ".$from_date." AND ".$to_date;
		   		if(!empty($search['adv_search'])){
		   			$s_where = array();
		   			$s_search = addslashes(trim($search['adv_search']));
		   			$s_where[] = " e.employeeCode LIKE '%{$s_search}%'";
		   			$s_where[] = " e.sureName LIKE '%{$s_search}%'";
		   			$s_where[] = " e.firstName LIKE '%{$s_search}%'";
		   			$s_where[] = " e.latinName LIKE '%{$s_search}%'";
		   			$s_where[] = " e.phoneNumber LIKE '%{$s_search}%'";
		   			$where .=' AND ('.implode(' OR ',$s_where).')';
		   		}
		   		if($search['status']>-1){
		   			$where.=" AND e.status=".$search['status'];
		   		}
		   		if(!empty($search['gender'])){
		   			$where.=" AND e.gender= '".$search['gender']."'";
		   		}
		   		$order=" ORDER BY e.`createDate` DESC";
		   		return $db->fetchAll($sql.$where.$order);
	   		}catch(exception $e){
	   			Application_Form_FrmMessage::message("Application Error");
	   			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
	   			$db->rollBack();
	   		}
   	}
    function insertEmployee($_data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
    		$part= PUBLIC_PATH.'/images/all/';
    		if(!file_exists($part)) {
    			mkdir($part, 0777, true);
    		}
    		$image_name = "";
    		$photo="";
    		$name = $_FILES['photo']['name'];
    		if (!empty($name)){
    			$ss = explode(".", $name);
    			$image_name = "employee_".$_data['employeeCode'].date("Y").date("m").date("d").time().".".end($ss);
    			$tmp = $_FILES['photo']['tmp_name'];
    			if(move_uploaded_file($tmp, $part.$image_name)){
    				$photo = $image_name;
    			}
    			else
    				$string = "Image Upload failed";
    		}
    		
    		$_arr=array(
    				'employeeCode'=> $_data['employeeCode'],
    				'sureName'	  => $_data['sureName'],
    				'firstName'	      => $_data['firstName'],
    				'latinName'      => $_data['latinName'],
    				'gender'      => $_data['gender'],
    				'dob'      => $_data['dob'],
    				'pob'	  => $_data['pob'],
    				'nation'	  => $_data['nation'],
    				'nationality'	  => $_data['nationality'],
    				'idCardNumber'	  => $_data['idCardNumber'],
    				'startWorkDate'      => $_data['startWorkDate'],
    				
    				'framework'	  => $_data['framework'],
    				'position'	      => $_data['position'],
    				'appointmentLetter'      => $_data['appointmentLetter'],
//     				'positionEqual'      => $_data['positionEqual'],
    				'organization'      => $_data['organization'],
    				'ministry'      => $_data['ministry'],
    				'educationLevel'      => $_data['educationLevel'],
    				'skill'      => $_data['skill'],
    				'language'	  => $_data['language'],
    				'lastLevel'      => $_data['lastLevel'],
    				'salaryUpdated'	      => $_data['salaryUpdated'],
    				'lastRaiseSalary'      => $_data['lastRaiseSalary'],
    				'oldWorkPlace'      => $_data['oldWorkPlace'],
    				'expectRetire'      => $_data['expectRetire'],
    				'workPlaceAddress'      => $_data['workPlaceAddress'],
    				'currentAddress'	      => $_data['currentAddress'],
    				'contactName'      => $_data['contactName'],
    				'phoneNumber'      => $_data['phoneNumber'],
    				'emailAddress'      => $_data['emailAddress'],
//     				'certification'      => $_data['certification'],
//     				'certificateCode'      => $_data['certificateCode'],
//     				'salary'	      => $_data['salary'],
    				
    				
    				
//     				'seniority'      => $_data['seniority'],
    				'profilePhoto'      => $photo,
    				'createDate'=> date("Y-m-d H:i:s"),
    				'status'      => 1,
    				'userId'      => $this->getUserId(),
    				'expiredDate'      => $_data['expiredDate'],
    				'familyStatus'      => $_data['familyStatus'],
    		);
    		$this->_name="tbl_employee";
    		$pro_id =$this->insert($_arr);
    		$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
    }
    function getEmployeeById($id){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$sql="SELECT d.*,
			(SELECT p.title FROM `tbl_position` AS p WHERE p.id = d.`position` LIMIT 1) AS positionTitle
    		FROM `tbl_employee` AS d WHERE 1";
    		$where=" AND d.`id` =".$id;
    		$order="";
    		$limit=" LIMIT 1";
    		return $db->fetchRow($sql.$where.$order.$limit);
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
    }
    function editEmployee($_data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
    		$part= PUBLIC_PATH.'/images/all/';
    		if (!file_exists($part)) {
    			mkdir($part, 0777, true);
    		}
    		$photo = "";;
    		$name = $_FILES['photo']['name'];
    		if (!empty($name)){
    			$ss = explode(".", $name);
    			$image_name = "employee_".$_data['employeeCode'].date("Y").date("m").date("d").time().".".end($ss);
    			$tmp = $_FILES['photo']['tmp_name'];
    			if(move_uploaded_file($tmp, $part.$image_name)){
    				$photo = $image_name;
    			}
    			else
    				$string = "Image Upload failed";
    		}
    
    		$_arr=array(
    				'employeeCode'=> $_data['employeeCode'],
    				'sureName'	  => $_data['sureName'],
    				'firstName'	      => $_data['firstName'],
    				'latinName'      => $_data['latinName'],
    				'gender'      => $_data['gender'],
    				'dob'      => $_data['dob'],
    				'pob'	  => $_data['pob'],
    				'nation'	  => $_data['nation'],
    				'nationality'	  => $_data['nationality'],
    				'idCardNumber'	  => $_data['idCardNumber'],
    				'startWorkDate'      => $_data['startWorkDate'],
    				'framework'	  => $_data['framework'],
    				'position'	      => $_data['position'],
    				'appointmentLetter'      => $_data['appointmentLetter'],
//     				'positionEqual'      => $_data['positionEqual'],
    				'organization'      => $_data['organization'],
    				'ministry'      => $_data['ministry'],
    				'educationLevel'      => $_data['educationLevel'],
    				'skill'      => $_data['skill'],
    				'language'	  => $_data['language'],
    				'lastLevel'      => $_data['lastLevel'],
    				'salaryUpdated'	      => $_data['salaryUpdated'],
    				'lastRaiseSalary'      => $_data['lastRaiseSalary'],
    				'oldWorkPlace'      => $_data['oldWorkPlace'],
    				'expectRetire'      => $_data['expectRetire'],
    				'workPlaceAddress'      => $_data['workPlaceAddress'],
    				'currentAddress'	      => $_data['currentAddress'],
    				'contactName'      => $_data['contactName'],
    				'phoneNumber'      => $_data['phoneNumber'],
    				'emailAddress'      => $_data['emailAddress'],
//     				'certification'      => $_data['certification'],
//     				'certificateCode'      => $_data['certificateCode'],
//     				'salary'	      => $_data['salary'],
    				
    				
    				
//     				'seniority'      => $_data['seniority'],
    				'modifyDate'=> date("Y-m-d H:i:s"),
    				'userId'      => $this->getUserId(),
    				
    				'expiredDate'      => $_data['expiredDate'],
    				'familyStatus'      => $_data['familyStatus'],
    		);
    		$this->_name="tbl_employee";
    		if (!empty($name)){
    			$_arr['profilePhoto'] = $photo;
    		}
    		$where =" id=".$_data['id'];
    		$this->update($_arr, $where);
    		$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
    }
    function updateStatus($us_id,$status){
    	$_user_data=array(
    			'status'=> $status
    	);
    	$where=$this->getAdapter()->quoteInto('id=?', $us_id);
    	return  $this->update($_user_data,$where);
    }
    function DeleteEmployee($us_id){
    	$_user_data=array(
    			'status'=> -1
    	);
    	$where=$this->getAdapter()->quoteInto('id=?', $us_id);
    	return  $this->update($_user_data,$where);
    }
	function getAllPosition(){
		$db = $this->getAdapter();
		$sql="SELECT p.`id`,p.`title` FROM `tbl_position` AS p WHERE p.`status`=1";
		return $db->fetchAll($sql);
	}
	function getEmployeeCode($employeecode){
		$db = $this->getAdapter();
		$sql="SELECT * FROM `tbl_employee` AS e WHERE e.`employeeCode`='$employeecode' AND e.`status`=1";
		$row = $db->fetchRow($sql);
		if (empty($row)){
			return 0;
		}else{
			return 1;
		}
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
	
	function getSettingByValue($label){
		$db = $this->getAdapter();
		$row="";
		if (!empty($label)){
			$sql="SELECT t.* FROM `tbl_setting` AS t WHERE t.`label`='$label' LIMIT 1";
			$row = $db->fetchRow($sql);
		}
		return $row;
	}
	
	
	
	
	
	
}

