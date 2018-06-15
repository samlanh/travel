<?php

class Other_Model_DbTable_DbPartner extends Zend_Db_Table_Abstract
{

    protected $_name = 'tp_partner';
    
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth_travel');
    	return $session_user->user_id;
    }
    
	 function getAllEmployee($search=NULL){
	   	$db = $this->getAdapter();
	   	$db->beginTransaction();
	   	try{
	   		$sql="select
	   					id,
	   					partnerName,
	   					ordering,
	   					note,
	   					createDate,
	   					(select first_name from rms_users where rms_users.id = userID) as user,
	   					(select name_kh from tp_view where type=1 and key_code = status) as status
	   				from 
	   					tp_partner
	   				where
	   					partnerName !=''	
	   			";
	   		
	   		$where="";
	   		
	   		$from_date =(empty($search['start']))? '1': " `createDate` >= '".date("Y-m-d",strtotime($search['start']))." 00:00:00'";
	   		$to_date = (empty($search['end']))? '1': " `createDate` <= '".date("Y-m-d",strtotime($search['end']))." 23:59:59'";
	   		$where.= " AND  ".$from_date." AND ".$to_date;
	   		
	   		if(!empty($search['adv_search'])){
	   			$s_where = array();
	   			$s_search = addslashes(trim($search['adv_search']));
	   			$s_where[] = " partnerName LIKE '%{$s_search}%'";
	   			$s_where[] = " ordering LIKE '%{$s_search}%'";
	   			$s_where[] = " note LIKE '%{$s_search}%'";
	   			$where .=' AND ('.implode(' OR ',$s_where).')';
	   		}
	   		if($search['status']>-1){
	   			$where.=" AND status=".$search['status'];
	   		}
	   		
	   		$order=" ORDER BY id DESC";
	   		return $db->fetchAll($sql.$where.$order);
	   		
   		}catch(exception $e){
   			Application_Form_FrmMessage::message("Application Error");
   			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
   			$db->rollBack();
   		}
   	}
    function addPartner($_data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
    		//////// for logo image /////////////////////////
    		$part= PUBLIC_PATH.'/images/all/partner/';
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
    				'partnerName'	=> $_data['partnerName'],
    				'ordering'		=> $_data['ordering'],
    				'note'	    	=> $_data['note'],
    				'photo'	  		=> $photo,
    				
    				'userID'    	=> $this->getUserId(),
    				'createDate'	=> date("Y-m-d H:i:s"),
    				'modifyDate'	=> date("Y-m-d H:i:s"),
    				
    				'status'      	=> 1,
    		);
    		$this->_name="tp_partner";
    		$this->insert($_arr);
    		
    		$db->commit();
    	}catch(exception $e){
    		$db->rollBack();
    		Application_Form_FrmMessage::message("Application Error");
    		echo $e->getMessage();exit();
    	}
    }
    
    function getPartnerById($id){
    	$db = $this->getAdapter();
    	$sql="SELECT 
    				*
    			FROM 
    				tp_partner
    			WHERE 
    				id = $id
    			limit 1	
    		";
    	return $db->fetchRow($sql);
    }
    
    function editPartner($_data,$id){
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
    				'partnerName'	=> $_data['partnerName'],
    				'ordering'		=> $_data['ordering'],
    				'note'	    	=> $_data['note'],
    				'photo'	  		=> $photo,
    				
    				'userID'    	=> $this->getUserId(),
    				'modifyDate'	=> date("Y-m-d H:i:s"),
    				
    				'status'      	=> $_data['status'],
    		);
    		$where = " id = $id ";
    		$this->_name="tp_partner";
    		$this->update($_arr, $where);
    		
    		$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
    }
    
	
}

