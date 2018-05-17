<?php
class Setting_generalController extends Zend_Controller_Action {
	
	
public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
	}
	public function indexAction()
	{
		$id = $this->getRequest()->getParam("id");
		if($this->getRequest()->isPost()){
			try{
				$data = $this->getRequest()->getPost();
				$db = new Setting_Model_DbTable_DbGeneral();
				$db->updateWebsitesetting($data);
				Application_Form_FrmMessage::message("ការ​បញ្ចូល​ជោគ​ជ័យ !");
				$this->_redirect("/setting/general");
			}catch (Exception $e){
				Application_Form_FrmMessage::message("EDIT_FAILE");
				echo $e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$db_gs = new Application_Model_DbTable_DbGlobalSelect();
		$row =array();
		$this->view->cardBackground = $db_gs->getWebsiteSetting('cardBackground');
// 		$fm = new Setting_Form_FrmGeneral();
// 		$frm = $fm->FrmGeneral($row);
// 		Application_Model_Decorator::removeAllDecorator($frm);
// 		$this->view->frm_general = $frm;
	}
	
}

