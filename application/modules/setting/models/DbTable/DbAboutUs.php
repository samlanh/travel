<?php

class Setting_Model_DbTable_DbAboutUs extends Zend_Db_Table_Abstract
{

    protected $_name = 'tp_about_us';
    public function getUserId(){
    	$db = new Application_Model_DbTable_DbGlobal();
		return $db->getUserId();   
    }
	public function getAllAboutUs($search){
		$db = $this->getAdapter();
		$sql = " SELECT * from tp_about_us ";
		return $db->fetchAll($sql);
	}
	function add($data){
		$db = $this->getAdapter();
		$arr = array(
				'aboutUs'	=>$data['aboutUs'],
				'contactUs'	=>$data['contactUs'],
				'email'		=>$data['email'],
				'facebook'	=>$data['facebook'],
				'youtube'	=>$data['youtube'],
				'instagram'	=>$data['instagram'],
				'twitter'	=>$data['twitter'],
			);
		$this->insert($arr);
	}
	function edit($data,$id){
		$db = $this->getAdapter();
		$arr = array(
			'aboutUs'	=>$data['aboutUs'],
			'contactUs'	=>$data['contactUs'],
			'email'		=>$data['email'],
			'facebook'	=>$data['facebook'],
			'youtube'	=>$data['youtube'],
			'instagram'	=>$data['instagram'],
			'twitter'	=>$data['twitter'],
		);
		$where = " id = $id";
		$this->update($arr, $where);
	}
	
	public function getAboutUs($id){
		$db = $this->getAdapter();
		$sql = " SELECT * FROM tp_about_us WHERE id = $id limit 1";
		return $db->fetchRow($sql);
	}
	
}

