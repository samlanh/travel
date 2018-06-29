<?php
class Other_PartnerController extends Zend_Controller_Action {
	
	const REDIRECT_URL = '/other/partner';
public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
	}
	public function indexAction()
	{
		if($this->getRequest()->isPost()){
			$search=$this->getRequest()->getPost();
		}
		else{
			$search = array(
					'adv_search' => '',
					'start'=> '',
					'end'=>date('Y-m-d'),
					'status'=>-1,
			);
		}
		
		$db = new Other_Model_DbTable_DbPartner();
		$rs_rows = $db->getAllPartner($search);
		
		$list = new Application_Form_Frmtable();
		$collumns = array("Partner Name","Ordering","Website","Note","Create Date","User","Status");
		$link = array(
				'module'=>'other','controller'=>'partner','action'=>'edit',
		);
		$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('partnerName'=>$link,'ordering'=>$link,'website'=>$link,'note'=>$link));
		
		$this->view->search = $search;
		
		
	}
	public function addAction()
	{
		$db = new Other_Model_DbTable_DbPartner();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			try{
				$id= $db->addPartner($data);
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL);
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
	}
	public function editAction(){
		$id = $this->getRequest()->getParam("id");
		$db = new Other_Model_DbTable_DbPartner();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			try{
				$db->editPartner($data,$id);
				Application_Form_FrmMessage::Sucessfull("UPDATE_SUCCESS",self::REDIRECT_URL);
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		
		$this->view->partner = $db->getPartnerById($id);
		
	}
	
	function deleteAction(){
		$id = $this->getRequest()->getParam('id');
		$id = (empty($id))? 0 : $id;
		
		$db_user = new Employee_Model_DbTable_DbEmployee();
		$db = $db_user->DeleteEmployee($id);
		$this->_redirect(self::REDIRECT_URL);
	}
}

