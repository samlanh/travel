<?php

class Booking_Model_DbTable_DbCustomer extends Zend_Db_Table_Abstract
{

    protected $_name = 'tp_supplier';
    
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth_travel');
    	return $session_user->user_id;
    }
    
	 function getAllEmployee($search=NULL){
	   	$db = $this->getAdapter();
	   	$db->beginTransaction();
	   	try{
	   		$from_date =(empty($search['start']))? '1': " `createDate` >= '".date("Y-m-d",strtotime($search['start']))." 00:00:00'";
	   		$to_date = (empty($search['end']))? '1': " `createDate` <= '".date("Y-m-d",strtotime($search['end']))." 23:59:59'";
	   		 
	   		$sql="select
	   					id,
	   					customerName,
	   					(select name_en from tp_view where type=2 and key_code = gender) as gender,
	   					tel,
	   					email,
	   					registerBy,
	   					(select name_en from tp_view where type=3 and key_code = isVerify) as isVerify,
	   					createDate,
	   					(select name_en from tp_view where type=1 and key_code = status) as status,
	   					'Password'
	   				from 
	   					tp_customer
	   				where
	   					customerName !=''	
	   			";
	   		$where="";
	   		$where.= " AND  ".$from_date." AND ".$to_date;
	   		if(!empty($search['adv_search'])){
	   			$s_where = array();
	   			$s_search = addslashes(trim($search['adv_search']));
	   			$s_where[] = " customerName LIKE '%{$s_search}%'";
	   			$s_where[] = " tel LIKE '%{$s_search}%'";
	   			$s_where[] = " email LIKE '%{$s_search}%'";
	   			$where .=' AND ('.implode(' OR ',$s_where).')';
	   		}
	   		if($search['status']>-1){
	   			$where.=" AND status=".$search['status'];
	   		}
	   		if($search['isVerify']!=''){
	   			$where.=" AND isVerify= '".$search['isVerify']."'";
	   		}
	   		$order=" ORDER BY `createDate` DESC";
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
    		$part= PUBLIC_PATH.'/images/all/customer/';
    		if (!file_exists($part)) {
    			mkdir($part, 0777, true);
    		}
    		$image_name = "";
    		$photo="";
    		$name = $_FILES['photo']['name'];
    		if (!empty($name)){
    			$ss = explode(".", $name);
    			$image_name = "photo_".time().".".end($ss);
    			$tmp = $_FILES['photo']['tmp_name'];
    			if(move_uploaded_file($tmp, $part.$image_name)){
    				$photo = $image_name;
    			}
    			else
    				$string = "Image Upload failed";
    		}
    		
    		$_arr=array(
    				'customerName'	=> $_data['customerName'],
    				'gender'		=> $_data['gender'],
    				'age'	    	=> $_data['age'],
    				'tel'      		=> $_data['tel'],
    				'email'      	=> $_data['email'],
    				'password'      => MD5($_data['password']), // $_data['password'],,
    				'isVerify'      => $_data['isVerify'],
    				'photo'	  		=> $photo,
    				
    				'registerBy'    => "admin",
    				'userID'    	=> $this->getUserId(),
    				'createDate'	=> date("Y-m-d H:i:s"),
    				'modifyDate'	=> date("Y-m-d H:i:s"),
    				'status'      	=> 1,
    		);
    		$this->_name="tp_customer";
    		$this->insert($_arr);
    		
    		$db->commit();
    	}catch(exception $e){
    		$db->rollBack();
    		Application_Form_FrmMessage::message("Application Error");
    		echo $e->getMessage();exit();
    	}
    }
    function getCustomerById($id){
    	$db = $this->getAdapter();
    	$sql="SELECT 
    				*
    			FROM 
    				tp_customer
    			WHERE 
    				id = $id
    			limit 1	
    		";
    	return $db->fetchRow($sql);
    }
    
    function editSupplyer($_data,$id){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
    		//////// for logo image /////////////////////////
    		$part= PUBLIC_PATH.'/images/all/customer/';
    		if (!file_exists($part)) {
    			mkdir($part, 0777, true);
    		}
    		$image_name = "";
    		$photo="";
    		$name = $_FILES['photo']['name'];
    		if (!empty($name)){
    			$ss = explode(".", $name);
    			$image_name = "photo_".time().".".end($ss);
    			$tmp = $_FILES['photo']['tmp_name'];
    			if(move_uploaded_file($tmp, $part.$image_name)){
    				$photo = $image_name;
    			}
    			else
    				$string = "Image Upload failed";
    		}else{
    			$photo = $_data['oldLogo'];
    		}
    		
    		$_arr=array(
    				'customerName'	=> $_data['customerName'],
    				'gender'		=> $_data['gender'],
    				'age'	    	=> $_data['age'],
    				'tel'      		=> $_data['tel'],
    				'email'      	=> $_data['email'],
    				'isVerify'      => $_data['isVerify'],
    				'photo'	  		=> $photo,
    				
    				'registerBy'    => "admin",
    				'userID'    	=> $this->getUserId(),
    				'modifyDate'	=> date("Y-m-d H:i:s"),
    				
    				'status'      	=> $_data['status'],
    		);
    		$where = " id = $id ";
    		$this->_name="tp_customer";
    		$this->update($_arr, $where);
    		
    		$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
    }
    
    function changePassword($data, $id){
    	$arr=array(
    		'password'=> MD5($data['newPassword'])
    	);
    
    	$where=$this->getAdapter()->quoteInto('id=?', $id);
    	$this->_name="tp_customer";
    	return  $this->update($arr,$where);
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
	function getAllMainSupplyer(){
		$db = $this->getAdapter();
		$sql="SELECT id,supplyerName as name FROM tp_supplier WHERE status=1 and isMainBranch=1 ";
		return $db->fetchAll($sql);
	}
	
}

