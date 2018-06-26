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
    
	 function getAllConditon($type){
		   	$db = $this->getAdapter();
		   	$db->beginTransaction();
		   	try{
		   		$sql="SELECT id,`conTitle`,DATE_FORMAT( `createDate`, '%d-%b-%Y') AS createdate,DATE_FORMAT( `modifyDate`,'%d-%b-%Y') AS modifydate,
						(SELECT v.name_en FROM `tp_view` AS v WHERE v.key_code=tp_condition.status AND v.type=1 LIMIT 1) AS status_name
					  FROM `tp_condition` WHERE `type`=$type AND `conTitle`!='' LIMIT 1";
		   		return $db->fetchRow($sql);
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
        //print_r($_data['id']);exit();
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$_arr=array(
    				'conTitle'		 => $_data['condition'],
    				'type'      	 => 1,
    				'createDate'	 => date("Y-m-d H:i:s"),
    				'modifyDate'	 => date("Y-m-d H:i:s"),
    				//'orderBy'		 => $_data['order_by'],
    				'status'         => 1,
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

