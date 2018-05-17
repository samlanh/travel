<?php

class Application_Model_DbTable_DbVdGlobal extends Zend_Db_Table_Abstract
{
	public function setName($name){
		$this->_name=$name;
	}
	public static function getUserId(){
		$db = new Application_Model_DbTable_DbGlobal();
		return $db->getUserId();
	}
	public function getLaguage(){
		$db = $this->getAdapter();
		$sql="SELECT * FROM `vd_language` AS l WHERE l.`status`=1";
		return $db->fetchAll($sql);
	}
	static function getCurrentLang(){
		$session_lang=new Zend_Session_Namespace('lang');
		$lang = $session_lang->lang_id;
		if(empty($lang)){
			$session_lang->lang_id=1;
			return 1;
		}else{return $lang;}
	}
}
?>