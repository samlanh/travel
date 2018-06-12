<?php

class Other_Model_DbTable_DbCondition extends Zend_Db_Table_Abstract
{

    protected $_name = 'tp_condition';
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
    
	 function getAllConditon($search=null){
		   	$db = $this->getAdapter();
		   	$db->beginTransaction();
		   	try{
// 		   		$from_date =(empty($search['start']))? '1': " e.`createDate` >= '".date("Y-m-d",strtotime($search['start']))." 00:00:00'";
// 		   		$to_date = (empty($search['end']))? '1': " e.`createDate` <= '".date("Y-m-d",strtotime($search['end']))." 23:59:59'";
		   		$sql="SELECT id,`conTitle`,DATE_FORMAT( `createDate`, '%d-%b-%Y') AS createdate,DATE_FORMAT( `modifyDate`,'%d-%b-%Y') AS modifydate,
						(SELECT v.name_en FROM `tp_view` AS v WHERE v.key_code=tp_condition.status AND v.type=1 LIMIT 1) AS status_name
					  FROM `tp_condition` WHERE `type`=1 AND `conTitle`!='' ";
		   		$where="";
// 		   		$where.= " AND  ".$from_date." AND ".$to_date;
		   		if(!empty($search['adv_search'])){
		   			$s_where = array();
		   			$s_search = addslashes(trim($search['adv_search']));
		   			$s_search = str_replace(' ', '', $s_search);
		   			$s_where[]="REPLACE(conTitle,' ','')   LIKE '%{$s_search}%'";
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
   	
    function addCondition($_data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$_arr=array(
    				'conTitle'		 => $_data['condition'],
    				'type'      	 => 1,
    				'createDate'	 => date("Y-m-d H:i:s"),
    				'modifyDate'	 => date("Y-m-d H:i:s"),
    				'orderBy'		 => $_data['order_by'],	
    				'status'         => 1,
    				'user_id'         => $this->getUserId(),
    		);
    		$this->_name="tp_condition";
    		$pro_id =$this->insert($_arr);
    		$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
    }
    
    function updateCondition($_data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$_arr=array(
    				'conTitle'		 => $_data['condition'],
    				'type'      	 => 1,
    				'createDate'	 => date("Y-m-d H:i:s"),
    				'modifyDate'	 => date("Y-m-d H:i:s"),
    				'orderBy'		 => $_data['order_by'],
    				'status'         => $_data['status'],
    				'user_id'         => $this->getUserId(),
    		);
    		$this->_name="tp_condition";
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
	
	function getConditionById($id){
		$db = $this->getAdapter();
		$sql="  SELECT * FROM `tp_condition` WHERE id=$id AND `type`=1";
		return $db->fetchRow($sql);
	}
	
}

