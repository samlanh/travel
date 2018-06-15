<?php
class Other_TestimonialController extends Zend_Controller_Action {
	
	const REDIRECT_URL = '/other/testimonial/';
	const REDIRECT_URL_ADD = '/other/testimonial/add';
public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
	}
	
	public function indexAction()
	{
		try {
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
				 
			}else{
				$search = array(
						'adv_search' => '',
						'status'=>1,
				);
			}
			$this->view->search=$search;
			$db=new Other_Model_DbTable_DbTestimonial();
			$result = $db->getAllTestimonial($search);
    		$list = new Application_Form_Frmtable();
    		$collumns = array("Person Name","Description","Create Date","Modify Date","STATUS");
    		$link=array(
    				'module'=>'other','controller'=>'testimonial','action'=>'edit',
    		);
    		$this->view->list=$list->getCheckList(0,$collumns, $result,array('description'=>$link,'person_naem'=>$link));
    		if (empty($result)){
    			$result = array('err'=>1, 'msg'=>'មិនទាន់មានទិន្នន័យនៅឡើយ!');
    		}		
    	} catch (Exception $e) {
    		$result = Application_Model_DbTable_DbGlobal::getResultWarning();
    	}
//     	$frm = new Booking_Form_FrmSearchInfomation();
//     	Application_Model_Decorator::removeAllDecorator($frm);
//     	$this->view->formFilter = $frm->filter();
    }
    
	public function addAction()
	{
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Other_Model_DbTable_DbTestimonial();
			try{
			    $id= $db->addTestimonial($data);
				if(isset($data['save_close'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL);
				} 
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL_ADD);
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
	}
	
	public function editAction(){
	    $db = new Other_Model_DbTable_DbTestimonial();
		$id=$this->getRequest()->getParam('id');
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			try{
				$data['id']=$id;
				$id= $db->editTestimonial($data);
				if(isset($data["save_close"])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL);
					$this->_redirect(self::REDIRECT_URL);
				}else {
				  Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL);
				  $this->_redirect(self::REDIRECT_URL);
				}
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL);
				$this->_redirect(self::REDIRECT_URL);
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$this->view->row=$db->getTermsById($id);
	}
	
	function updateAction(){
		$blocked = $this->getRequest()->getParam('blocked');
		$blocked = (empty($blocked))? 0 : $blocked;
		
		$us_id = $this->getRequest()->getParam('id');
		$us_id = (empty($us_id))? 0 : $us_id;
		
		$db_user = new Other_Model_DbTable_DbTestimonial();
		$db = $db_user->updateStatus($us_id,$blocked);
		$this->_redirect(self::REDIRECT_URL);
	}
}

