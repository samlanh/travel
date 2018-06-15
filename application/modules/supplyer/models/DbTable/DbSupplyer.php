<?php

class Supplyer_Model_DbTable_DbSupplyer extends Zend_Db_Table_Abstract
{

    protected $_name = 'tp_supplier';
    
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth_travel');
    	return $session_user->user_id;
    }
    
	 function getAllSupplyer($search=NULL){
	   	$db = $this->getAdapter();
	   	$db->beginTransaction();
	   	try{
	   		$from_date =(empty($search['start']))? '1': " s.`createDate` >= '".date("Y-m-d",strtotime($search['start']))." 00:00:00'";
	   		$to_date = (empty($search['end']))? '1': " s.`createDate` <= '".date("Y-m-d",strtotime($search['end']))." 23:59:59'";
	   		 
	   		$sql="SELECT 
	   					 s.id,
		   				 s.supplyerName,
		   				 s.tel,
		   				 s.email,
		   				 s.website,
		   				 s.createDate,
		   				 (select first_name from rms_users where rms_users.id = s.userInsert) as user,
		   				 (select name_kh from tp_view where tp_view.type=1 and tp_view.key_code = s.status) as status
					FROM 
						tp_supplier AS s 
					WHERE 
						s.supplyerName != ''
	   			";
	   		$where="";
	   		$where.= " AND  ".$from_date." AND ".$to_date;
	   		if(!empty($search['adv_search'])){
	   			$s_where = array();
	   			$s_search = addslashes(trim($search['adv_search']));
	   			$s_where[] = " s.supplyerName LIKE '%{$s_search}%'";
	   			$s_where[] = " s.tel LIKE '%{$s_search}%'";
	   			$s_where[] = " s.email LIKE '%{$s_search}%'";
	   			$where .=' AND ('.implode(' OR ',$s_where).')';
	   		}
	   		if($search['status']>-1){
	   			$where.=" AND s.status=".$search['status'];
	   		}
	   		
	   		$order=" ORDER BY s.`id` DESC";
	   		
	   		return $db->fetchAll($sql.$where.$order);
   		}catch(exception $e){
   			Application_Form_FrmMessage::message("Application Error");
   			echo $e->getMessage();
   			$db->rollBack();
   		}
   	}
    function addSupplyer($_data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
    		//////// for logo image /////////////////////////
    		$part= PUBLIC_PATH.'/images/all/supplyerlogo/';
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
    		$part = PUBLIC_PATH.'/images/all/supplyeractivityphoto/';
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
    
    function editSupplyer($_data,$id){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
    		//////// for logo image /////////////////////////
    		$part= PUBLIC_PATH.'/images/all/supplyerlogo/';
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
    		}else{
    			$logo = $_data['oldLogo'];
    		}
    		//////// for activity photo /////////////////////////
    		$activity_photo = "";
    		$part = PUBLIC_PATH.'/images/all/supplyeractivityphoto/';
    		if (!file_exists($part)) {
    			mkdir($part, 0777, true);
    		}
    		$stu_photo_name = $_FILES['photo']['name'];
    		
    		if(!empty($stu_photo_name)){
    			if($stu_photo_name[0]!=null){
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
    			}else{
    				$activity_photo = $_data['oldActivityPhoto'];
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
    				
    				'userEdit'    	=> $this->getUserId(),
    				'modifyDate'	=> date("Y-m-d H:i:s"),
    				'status'      	=> 1,
    		);
    		$where = " id = $id ";
    		$this->_name="tp_supplier";
    		$this->update($_arr, $where);
    		
    		$i=0;
    		if($stu_photo_name[0]!=null){
    			$this->_name="tp_supplier_images";
    			$where1="supplyerId = $id ";
    			$this->delete($where1);
    			
    			$array = explode(",", $activity_photo);
    			foreach ($array as $ids){
    				$arr = array(
    					'supplyerId'=>$id,	
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
	function getAllMainSupplyer(){
		$db = $this->getAdapter();
		$sql="SELECT id,supplyerName as name FROM tp_supplier WHERE status=1 and isMainBranch=1 ";
		return $db->fetchAll($sql);
	}
	
}

