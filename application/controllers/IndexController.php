<?php

class IndexController extends Zend_Controller_Action
{

	const REDIRECT_URL = '/transfer';
	
    public function init()
    {
        /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');  
    }

    public function indexAction()
    {
    	$session_lang=new Zend_Session_Namespace('lang');
    	$session_lang->lang_id=2;
    	$url="http://qr.mihcambodia.com/public";
    	// action body
        /* set this to login page to change the character charset of browsers to Utf-8  ...*/
    	$session_user=new Zend_Session_Namespace('auth_travel');
    	$information = $this->getRequest()->getParam('information');
    	$this->view->information = $information;
    	$dbemployee = new Employee_Model_DbTable_DbEmployee();
    	if (!empty($session_user->user_id)){
    		
    		if (!empty($information)){
    			$employee = $dbemployee->getEmployeeByCode($information);
    			if (!empty($employee)){
    				
    				//$this->_redirect($url."/index/employee?information=".$employee['employeeCode']);
     				$this->_redirect("/employee/index/edit/id/".$employee['id']);
    			}
    		}
    		$this->_redirect("/employee");
    	}
    	$this->_helper->layout()->disableLayout();
		if($this->getRequest()->isPost())		
		{
			$formdata=$this->getRequest()->getPost();
				$session_lang=new Zend_Session_Namespace('lang');
				$session_lang->lang_id=2;//for creat session
				Application_Form_FrmLanguages::getCurrentlanguage($session_lang->lang_id);//for choose lang for when login
				
				$user_name=	$formdata['user_name'];
				$password= $formdata['txt_password'];
				$db_user=new Application_Model_DbTable_DbUsers();
				if($db_user->userAuthenticate($user_name,$password)){					
					
					$user_id=$db_user->getUserID($user_name);
					$user_info = $db_user->getUserInfo($user_id);
					
					$arr_acl=$db_user->getArrAcl($user_info['user_type']);
					$session_user->url_report=$db_user->getArrAclReport($user_info['user_type']);
					$session_user->user_id=$user_id;
					$session_user->user_name=$user_info['user_name'];
					$session_user->pwd=$password;		
					$session_user->level= $user_info['user_type'];
					$session_user->last_name= $user_info['last_name'];
					$session_user->first_name= $user_info['first_name'];
					$session_user->theme_style=1;
					
					$a_i = 0;
					$arr_actin = array();	
					for($i=0; $i<count($arr_acl);$i++){
						$arr_module[$i]=$arr_acl[$i]['module'];
					}
						
					$arr_module=(array_unique($arr_module));
					$arr_actin=(array_unique($arr_actin));
					$arr_module=$this->sortMenu($arr_module);
					
					$session_user->arr_acl = $arr_acl;
					$session_user->arr_module = $arr_module;
					$session_user->arr_actin = $arr_actin;
						
					$session_user->lock();
					
					$log=new Application_Model_DbTable_DbUserLog();
					$log->insertLogin($user_id);
					$information = empty($formdata['information'])?"":$formdata['information'];
					if (!empty($information)){
						$employee = $dbemployee->getEmployeeByCode($information);
						if (!empty($employee)){
							$this->_redirect("/employee/index/edit/id/".$employee['id']);
							//$this->_redirect($url."/index/employee?information=".$employee['employeeCode']);
						}
					}else{
						Application_Form_FrmMessage::redirectUrl("/employee");	
					}
					exit();
				}
				else{					
					$this->view->msg = 'Invalid Email and Password. Please try again! ';
				}
		}
    }

    function dashboardAction(){
		$this->_helper->layout()->disableLayout();
	}
    protected function sortMenu($menus){
    	$menus_order = Array ( 'home','employee','booking','rsvacl','setting');
    	$temp_menu = Array();
    	$menus=array_unique($menus);
    	foreach ($menus_order as $i => $val){
    		foreach ($menus as $k => $v){
    			if($val == $v){
    				$temp_menu[] = $val;
    				unset($menus[$k]);
    				break;
    			}
    		}
    	}
    	return $temp_menu;    	
    }

    public function logoutAction()
    {
        // action body
       // $this->_redirect("/index");
        if($this->getRequest()->getParam('value')==1){        	
        	$aut=Zend_Auth::getInstance();
        	$aut->clearIdentity();        	
        	$session_user=new Zend_Session_Namespace('auth_travel');
        	
        	$log=new Application_Model_DbTable_DbUserLog();
			$log->insertLogout($session_user->user_id);
			
        	$session_user->unsetAll();       	
	        if (empty($session_user->user_id)){ 
        	Application_Form_FrmMessage::redirectUrl("/");
	        }
        	exit();
        } 
    }
    public function logoutdetailAction()
    {
    	$dbemployee = new Employee_Model_DbTable_DbEmployee();
    	$information = $this->getRequest()->getParam('information');
    	$session_user=new Zend_Session_Namespace('auth_travel');
    	$session_user->unsetAll();
   		if (!empty($information)){
    		$employee = $dbemployee->getEmployeeByCode($information);
    		if (!empty($employee)){
    			$this->_redirect("/index/employee?information=".$employee['employeeCode']);
    		}
    	}
    	$this->_redirect("/");
    }
    public function changepasswordAction()
    {
        // action body
        if ($this->getRequest()->isPost()){ 
			$session_user=new Zend_Session_Namespace('auth_travel');    		
    		$pass_data=$this->getRequest()->getPost();
    		if ($pass_data['password'] == $session_user->pwd){
    			    			 
				$db_user = new Application_Model_DbTable_DbUsers();				
				try {
					$db_user->changePassword($pass_data['new_password'], $session_user->user_id);
					$session_user->unlock();	
					$session_user->pwd=$pass_data['new_password'];
					$session_user->lock();
					Application_Form_FrmMessage::Sucessfull('ការផ្លាស់ប្តូរដោយជោគជ័យ', self::REDIRECT_URL);
				} catch (Exception $e) {
					Application_Form_FrmMessage::message('ការផ្លាស់ប្តូរត្រូវបរាជ័យ');
				}				
    		}
    		else{
    			Application_Form_FrmMessage::message('ការផ្លាស់ប្តូរត្រូវបរាជ័យ');
    		}
        }   
    }

    public function errorAction()
    {
        // action body
    }
    public static function start(){
    	return ($this->getRequest()->getParam('limit_satrt',0));
    }
    function changelangeAction(){
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		$session_lang=new Zend_Session_Namespace('lang');
    		$session_lang->lang_id=$data['lange'];
    		Application_Form_FrmLanguages::getCurrentlanguage($data['lange']);
    		print_r(Zend_Json::encode(2));
    		exit();
    	}
    }

	function employeeAction(){
		$information = $this->getRequest()->getParam('information');
		$db = new Employee_Model_DbTable_DbEmployee();
		$url="http://qr.mihcambodia.com/public";
		if (!empty($information)){
			//$this->_redirect($url."/index/employee?information=".$information);
			$employee = $db->getEmployeeByCode($information);
			$this->view->employee = $employee;
		}
	}
}



