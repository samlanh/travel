<?php

class RsvAcl_UserController extends Zend_Controller_Action
{
	const REDIRECT_URL = '/rsvacl/user';
	const MAX_USER = 20;
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
	private $user_typelist = array();
	
    public function init()
    {
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    	
    	$db=new Application_Model_DbTable_DbGlobal();
    	$sql = "SELECT u.user_type_id,u.user_type FROM `rms_acl_user_type` u where u.`status`=1";
    	$results = $db->getGlobalDb($sql);
		foreach ($results as $key => $r){
			$this->user_typelist[$r['user_type_id']] = $r['user_type'];    
		}		
    }

    public function indexAction()
    {
		$db_user=new Application_Model_DbTable_DbUsers();
                
        $this->view->activelist =$this->activelist;       
        $this->view->user_typelist =$this->user_typelist;   
        $this->view->active =-1;
        
        $_data = array(
        	'active'=>-1,
        	'user_type'=>-1,
        	'txtsearch'=>''
        );
        if($this->getRequest()->isPost()){     	
        	$_data=$this->getRequest()->getPost();
        }
        $rs_rows = $db_user->getUserList($_data);
        $_rs = array();
        foreach ($rs_rows as $key =>$rs){
        	$_rs[$key] =array(
        	'id'=>$rs['id'],
        	'name'=>$rs['name'],
        	'email'=>$rs['email'],
        	'user_type'=>$this->user_typelist[$rs['user_type']],
        	'status'=>$rs['status']);
        }
        $list = new Application_Form_Frmtable();
        if(!empty($_rs)){
        	$glClass = new Application_Model_GlobalClass();
        	$rs_rows = $glClass->getImgActive($_rs, BASE_URL, true);
        }
        else{
        	$result = Application_Model_DbTable_DbGlobal::getResultWarning();
        }
        $collumns = array("FULL_NAME","EMAIL","USER_TYPE","STATUS");
        $link=array(
        		'module'=>'rsvacl','controller'=>'user','action'=>'edit',
        );
        $this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('user_name'=>$link,'name'=>$link));
    }
	public function addAction()
	{
			// action body
			$db_user=new Application_Model_DbTable_DbUsers();
			 
			if ($db_user->getMaxUser() > self::MAX_USER) {
				Application_Form_FrmMessage::Sucessfull('អ្នក​ប្រើ​ប្រាស់​របស់​អ្នក​បាន​ត្រឹម​តែ '.self::MAX_USER.' នាក់ ទេ!', self::REDIRECT_URL);
			}
			 
			$this->view->user_typelist =$this->user_typelist;
		
			if($this->getRequest()->isPost()){
				$userdata=$this->getRequest()->getPost();
				try {
					$db = $db_user->insertUser($userdata);
					$this->_redirect(self::REDIRECT_URL);
// 					Application_Form_FrmMessage::Sucessfull('ការ​បញ្ចូល​​ជោគ​ជ័យ', self::REDIRECT_URL);
				} catch (Exception $e) {
					$this->view->msg = 'ការ​បញ្ចូល​មិន​ជោគ​ជ័យ';
				}
			}
	}
	public function editAction()
	    {
	        // action body
	        $us_id = $this->getRequest()->getParam('id');
	    	$us_id = (empty($us_id))? 0 : $us_id;
	    	
	        $db_user=new Application_Model_DbTable_DbUsers();
	        $this->view->user_edit = $db_user->getUserEdit($us_id);
	
	        $this->view->user_typelist =$this->user_typelist;  
	        
	    	if($this->getRequest()->isPost()){
				$userdata=$this->getRequest()->getPost();	
				
				try {
					$db = $db_user->updateUser($userdata);		
					$this->_redirect(self::REDIRECT_URL);
// 					Application_Form_FrmMessage::Sucessfull('ការ​បញ្ចូល​​ជោគ​ជ័យ', self::REDIRECT_URL);		
				} catch (Exception $e) {
					$this->view->msg = 'ការ​បញ្ចូល​មិន​ជោគ​ជ័យ';
				}
			}
    }
    function profileAction(){
    	$db_user=new Application_Model_DbTable_DbUsers();
    	$session_user=new Zend_Session_Namespace('auth_travel');
    	if (!empty($session_user->user_id)){
    		$userinfo = $db_user->getUserInfomation($session_user->user_id);
    		$this->view->profile = $userinfo;
    	}
    	
    	
    }
 
    public function changepasswordAction()
	{
		if ($this->getRequest()->isPost()){ 
			$session_user=new Zend_Session_Namespace('auth_travel');    		
    		$pass_data=$this->getRequest()->getPost();
    		if ($pass_data['current_password'] == $session_user->pwd){
    			    			 
				$db_user = new Application_Model_DbTable_DbUsers();				
				try {
					$db_user->changePassword($pass_data['password'], $session_user->user_id);
					$session_user->unlock();	
					$session_user->pwd=$pass_data['password'];
					$session_user->lock();
					$this->view->msg ="Your password has been change successfully!";
					Application_Form_FrmMessage::Sucessfull('ការផ្លាស់ប្តូរដោយជោគជ័យ', "/rsvacl/user");
					
				} catch (Exception $e) {
					Application_Form_FrmMessage::message('ការផ្លាស់ប្តូរត្រូវបរាជ័យ');
				}				
    		}else{
    			$this->view->msg ="Please check your old password was wrong!";
    			Application_Form_FrmMessage::message('ការផ្លាស់ប្តូរត្រូវបរាជ័យ');
    		}
        }   
		
	}
	public function updateAction()
	{
		$status = $this->getRequest()->getParam('status');
		$status = (empty($status))? 0 : $status;
		
		$us_id = $this->getRequest()->getParam('id');
		$us_id = (empty($us_id))? 0 : $us_id;
		
		$db_user = new Application_Model_DbTable_DbUsers();
		$db = $db_user->updateStatus($us_id,$status);
		$this->_redirect(self::REDIRECT_URL);
	}
}
