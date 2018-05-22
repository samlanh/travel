<?php

class Supplyer_Model_DbTable_DbSupplyer extends Zend_Db_Table_Abstract
{

    protected $_name = 'tp_supplier';
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
    function addSupplyer($_data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
    		//////// for logo image /////////////////////////
    		$part= PUBLIC_PATH.'/images/supplyerlogo/';
    		if (!file_exists($part)) {
    			mkdir($part, 0777, true);
    		}
    		$image_name = "";
    		$logo="";
    		$name = $_FILES['logo']['name'];
    		if (!empty($name)){
    			$ss = explode(".", $name);
    			$image_name = "logo_".time().".".end($ss);
    			$tmp = $_FILES['logo']['tmp_name'];
    			if(move_uploaded_file($tmp, $part.$image_name)){
    				$logo = $image_name;
    			}
    			else
    				$string = "Image Upload failed";
    		}
    		//////// for activity photo /////////////////////////
    		$activity_photo = "";
    		$part = PUBLIC_PATH.'/images/activityphoto/';
    		if (!file_exists($part)) {
    			mkdir($part, 0777, true);
    		}
    		$stu_photo_name = $_FILES['photo']['name'];
    		if (!empty($stu_photo_name)){
    			foreach($stu_photo_name as $key=>$tmp_name){
    				$tem = explode(".", $stu_photo_name[$key]);
    				$image_name = time().$key.".".end($tem);
    				$tmp = $_FILES['photo']['tmp_name'][$key];
    				if(move_uploaded_file($tmp, $part.$image_name)){
    					move_uploaded_file($tmp, $part.$image_name);
    					if($key==0){
    						$comma = "";
    					}else{
    						$comma = ",";
    					}
    					$activity_photo = $activity_photo.$comma.$image_name;
    				}
    			}
    		}
    		
    		$_arr=array(
    				'supplyerName'	=> $_data['supplyerName'],
    				'isMainBranch'	=> $_data['isMainBranch'],
    				'parent'	    => $_data['parent'],
    				'tel'      		=> $_data['tel'],
    				'email'      	=> $_data['email'],
    				'website'      	=> $_data['website'],
    				'aboutUs'	  	=> $_data['aboutUs'],
    				'cancelPolicy'	=> $_data['cancelPolicy'],
    				'map'	  		=> $_data['map'],
    				
    				'logo'	  		=> $logo,
    				'activityPhoto' => $activity_photo,
    				
    				'userInsert'    => $this->getUserId(),
    				'createDate'	=> date("Y-m-d H:i:s"),
    				'modifyDate'	=> date("Y-m-d H:i:s"),
    				'status'      	=> 1,
    		);
    		$this->_name="tp_supplier";
    		$supplyer_id =$this->insert($_arr);
    		
    		$i=0;
    		if(!empty($activity_photo)){
    			$array = explode(",", $activity_photo);
    			foreach ($array as $id){
    				$arr = array(
    					'supplyerId'=>$supplyer_id,	
    					'imageName'=>$array[$i],
    				);
    				$i++;
    				$this->_name="tp_supplier_images";
    				$this->insert($arr);
    			}
    		}
    		
    		$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		echo $e->getMessage();exit();
    		$db->rollBack();
    	}
    }
    function getSupplyerById($id){
    	$db = $this->getAdapter();
    	$sql="SELECT 
    				d.*
    			FROM 
    				tp_supplier AS d 
    			WHERE 
    				d.id = $id
    			limit 1	
    		";
    	return $db->fetchRow($sql);
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

