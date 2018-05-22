<?php

class Employee_Model_DbTable_DbPosition extends Zend_Db_Table_Abstract
{

    protected $_name = 'tbl_position';
    public function getUserId(){
    	$db = new Application_Model_DbTable_DbGlobal();
		return $db->getUserId();   
    } 
	 function getAllPosition($search=NULL){
		   	$db = $this->getAdapter();
		   	$db->beginTransaction();
		   	try{
		   		$sql="SELECT d.* FROM `tbl_position` AS d WHERE 1 AND d.status > -1";
		   		$where="";
		   		$order=" ORDER BY d.`createDate` DESC";
		   		return $db->fetchAll($sql.$where.$order);
	   		}catch(exception $e){
	   			Application_Form_FrmMessage::message("Application Error");
	   			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
	   			$db->rollBack();
	   		}
   	}
    function insertPosition($_data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$_arr=array(
    				'title'=> $_data['title'],
    				'description'	  => $_data['description'],
    				'userId'      => $this->getUserId(),
    				'createDate'=> date("Y-m-d H:i:s"),
    		);
    		$this->_name="tbl_position";
    		if (!empty($_data['id'])){
    			$_arr['modifyDate'] = date("Y-m-d H:i:s");
    			$positionId = $_data['id'];
    			$where = " id = ".$positionId;
    			$this->update($_arr, $where);
    		}else{
    			$_arr['status'] = 1;
    			$_arr['createDate'] = date("Y-m-d H:i:s");
    			$positionId =$this->insert($_arr);
    		}
    		$db->commit();
    		return $positionId;
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
    }
    function getPositionById($id){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$sql="SELECT d.* FROM `tbl_position` AS d WHERE 1";
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
    
    function updateStatus($us_id,$status){
    	$_user_data=array(
    			'status'=> $status
    	);
    	$where=$this->getAdapter()->quoteInto('id=?', $us_id);
    	return  $this->update($_user_data,$where);
    }

	
	
	
	
	
	
	
	
	
	
}

