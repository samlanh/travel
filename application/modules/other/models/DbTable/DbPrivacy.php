<?php

class Other_Model_DbTable_DbPrivacy extends Zend_Db_Table_Abstract
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
    
	 function getAllPrivacy($type){
		   	$db = $this->getAdapter();
		   	$db->beginTransaction();
		   	try{
// 		   		$from_date =(empty($search['start']))? '1': " e.`createDate` >= '".date("Y-m-d",strtotime($search['start']))." 00:00:00'";
// 		   		$to_date = (empty($search['end']))? '1': " e.`createDate` <= '".date("Y-m-d",strtotime($search['end']))." 23:59:59'";
		   		$sql="SELECT id,`description`,DATE_FORMAT(`createDate`, '%d-%M-%Y'),DATE_FORMAT(`modifyDate`,'%d-%M-%Y'),
                        (SELECT v.name_en FROM `tp_view` AS v WHERE v.key_code=tp_privacy.`status` AND v.type=1 LIMIT 1) AS `status`
                        FROM `tp_privacy`
                        WHERE `description`!='' AND type=$type LIMIT 1";
		   		return $db->fetchRow($sql);
	   		}catch(exception $e){
	   			Application_Form_FrmMessage::message("Application Error");
	   			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
	   			$db->rollBack();
	   		}
   	}
   	
    function addPrivacy($_data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$_arr=array(
    		        'description'    => $_data['disciption'],
    				'createDate'	 => date("Y-m-d H:i:s"),
    				'modifyDate'	 => date("Y-m-d H:i:s"),
    				'orderBy'		 => $_data['order_by'],	
    				'status'         => 1,
    		        'type'           => 1,
    				'user_id'        => $this->getUserId(),
    		);
    		$this->_name="tp_privacy";
    		$pro_id =$this->insert($_arr);
    		$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
    }
    
    function editPrivacy($_data){
        
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    	    $_arr=array(
    	        'description'    => $_data['disciption'],
    	        'modifyDate'	 => date("Y-m-d H:i:s"),
    	        //'orderBy'		 => $_data['order_by'],
    	        'status'         => 1,
    	        'type'           => 1,
    	        'user_id'        => $this->getUserId(),
    	    );
    		$this->_name="tp_privacy";
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
	
	function getTermsById($id){
		$db = $this->getAdapter();
		$sql=" SELECT * FROM `tp_privacy` WHERE `type`=1 AND id=$id";
		return $db->fetchRow($sql);
	}
	
}

