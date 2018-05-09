<?php
class Global_BranchController extends Zend_Controller_Action {
	const REDIRECT_URL='/global';
	protected $tr;
	public function init()
	{
		$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
		/* Initialize action controller here */
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
      try{
    	$db = new Global_Model_DbTable_DbBranch();
    	 if($this->getRequest()->isPost()){
    	$search=$this->getRequest()->getPost();
   		}
     else{
   		 $search = array(
      		'adv_search' => '',
      		'status_search' => -1);
  		 }
           $rs_rows= $db->getAllBranch($search);
           $glClass = new Application_Model_GlobalClass();
			$rs_rowshow = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("BRANCH_NAME","PARENT_BRANCH","PREFIX_CODE","CODE","ADDRESS","PHONE","FAX","OTHERS","STATUS");
			$link=array(
					      'module'=>'global','controller'=>'branch','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rowshow,array('branch_namekh'=>$link,'branch_nameen'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message($this->tr->translate("APPLICATION_ERROR"));
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo $e->getMessage();
		}
		$fm = new Global_Form_Frmbranch();
		$frm = $fm->Frmbranch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_branch = $frm;
  
	}
	
	function addAction()
	{
		if($this->getRequest()->isPost()){//check condition return true click submit button
			$_data = $this->getRequest()->getPost();
			$_dbmodel = new Global_Model_DbTable_DbBranch();
			try {
				$_dbmodel->addbranch($_data);
				if(!empty($_data['save_close'])){
					Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL ."/branch/index");
				}else{
					Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL ."/branch/add");
				}
			}catch (Exception $e) {
				Application_Form_FrmMessage::message($this->tr->translate("INSERT_FAIL"));
				echo $e->getMessage();exit();
			}
		}
		$fm = new Global_Form_Frmbranch();
		$frm = $fm->Frmbranch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_branch = $frm;
	}
	function editAction(){
		$id=$this->getRequest()->getParam("id");
		if($this->getRequest()->isPost())
		{
			$data = $this->getRequest()->getPost();
			$db = new Global_Model_DbTable_DbBranch();
			try{
				$db->updateBranch($data,$id);
				Application_Form_FrmMessage::Sucessfull($this->tr->translate("EDIT_SUCCESS"),self::REDIRECT_URL."/branch/index");
			}catch (Exception $e){
				Application_Form_FrmMessage::message($this->tr->translate("EDIT_FAIL"));
				echo $e->getMessage();exit();
			}
		}
		$db=new Global_Model_DbTable_DbBranch();
		$row=$db->getBranchById($id);
	
		$frm= new Global_Form_Frmbranch();
		$update=$frm->FrmBranch($row);
		$this->view->frm_branch=$update;
		Application_Model_Decorator::removeAllDecorator($update);
	}
	
	function addbranchAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
		
    		$db = new Global_Model_DbTable_DbBranch();
    		$gty= $db->addajaxs($data);
    		print_r(Zend_Json::encode($gty));
    		exit();
    	}
    
    }
}

