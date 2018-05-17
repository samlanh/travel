<?php

class Application_Model_DbTable_DbGlobalSelect extends Zend_Db_Table_Abstract
{
	public function setName($name){
		$this->_name=$name;
	}
	public static function getUserId(){
		$db = new Application_Model_DbTable_DbGlobal();
		return $db->getUserId();
	}
	static function getCurrentLang(){
		$session_lang=new Zend_Session_Namespace('lang');
		if(!empty($session_lang->lang_id)){
			return $session_lang->lang_id;
		}else{
			return 1;
		}
	}
	public function getWebsiteSetting($label){
		$db = $this->getAdapter();
		$sql="SELECT * FROM `tbl_setting` AS ws WHERE ws.`label`='$label' AND ws.`status`=1 limit 1";
		return $db->fetchRow($sql);
	}
	
}
?>