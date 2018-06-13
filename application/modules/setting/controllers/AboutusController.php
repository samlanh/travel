<?php
class Setting_AboutusController extends Zend_Controller_Action {
	
	public function init()
	{
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction()
	{
		try{
			$db = new Setting_Model_DbTable_DbAboutUs();
			$rs_rows= $db->getAllAboutUs($search=null);//call frome model
			$list = new Application_Form_Frmtable();
			$collumns = array("About Us","Contact Us","Email","Facebook","Youtube","Instagram","Twitter");
			$link=array(
					'module'=>'setting','controller'=>'aboutus','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns,$rs_rows,array('aboutUs'=>$link,'status'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			echo $e->getMessage();
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	function addAction(){
		$this->_redirect('/setting/aboutus');
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Setting_Model_DbTable_DbAboutUs();
			$db->add($data);
			Application_Form_FrmMessage::Sucessfull('ការបញ្ចូលជោគ​ជ័យ','/setting/aboutus');
		}
	}
	function editAction(){
		$id = $this->getRequest()->getParam("id");
		$db  = new Setting_Model_DbTable_DbAboutUs();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db->edit($data,$id);
			Application_Form_FrmMessage::Sucessfull('ការកែប្រែ​​ជោគ​ជ័យ','/setting/aboutus');
		}
		$this->view->row = $db->getAboutUs($id);
	}
}

