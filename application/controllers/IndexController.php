<?php

class IndexController extends Zend_Controller_Action
{

	const REDIRECT_URL = '/home';
	
    public function init()
    {
        /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');  
    }

    public function indexAction()
    {
    	$session_user=new Zend_Session_Namespace('authstu');
    	$username = $session_user->first_name;
    	$user_id = $session_user->user_id;
    	if (!empty($user_id)){
    		$this->_redirect("/home");
    	}
    	$this->_helper->layout()->disableLayout();
		$form=new Application_Form_FrmLogin();				
		$form->setAction('index');		
		$form->setMethod('post');
		$form->setAttrib('accept-charset', 'utf-8');
		$this->view->form=$form;
		$key = new Application_Model_DbTable_DbKeycode();
		$this->view->data=$key->getKeyCodeMiniInv(TRUE);		
        
		if($this->getRequest()->isPost())		
		{
			$formdata=$this->getRequest()->getPost();
			if($form->isValid($formdata))
			{
				$session_lang=new Zend_Session_Namespace('lang');
				$session_lang->lang_id=$formdata["lang"];//for creat session
				Application_Form_FrmLanguages::getCurrentlanguage($formdata["lang"]);//for choose lang for when login
				$user_name=$form->getValue('txt_user_name');
				$password=$form->getValue('txt_password');
				$db_user=new Application_Model_DbTable_DbUsers();
				if($db_user->userAuthenticate($user_name,$password)){					
					
					$session_user=new Zend_Session_Namespace('authstu');
					$user_id=$db_user->getUserID($user_name);
					$user_info = $db_user->getUserInfo($user_id);
					$arr_acl=$db_user->getArrAcl($user_info['user_type']);
					$a_i = 0;
					$arr_actin = array();
					$arr_module=array();
					for($i=0; $i<count($arr_acl);$i++){
						$arr_module[$i]=$arr_acl[$i]['module'];
					}
					$arr_module=(array_unique($arr_module));
					$arr_actin=(array_unique($arr_actin));
					$arr_module=$this->sortMenu($arr_module);
						
					$session_user->arr_acl = $arr_acl;
					$session_user->arr_module = $arr_module;
					$session_user->arr_actin = $arr_actin;
					
					$session_user->user_id=$user_id;
					$session_user->user_name=$user_name;
					$session_user->pwd=$password;
					$session_user->level= $user_info['user_type'];
					$session_user->last_name= $user_info['last_name'];
					$session_user->first_name= $user_info['first_name'];
					$session_user->branch_id= $user_info['branch_id'];
					
// 					$session_user->url_report = $db_user->getArrAclReport($user_info['user_type']);
// 					for($i=0; $i<count($arr_acl);$i++){
// 						$arr_module[$i]=$arr_acl[$i]['module'];
// 						if($arr_acl[$i]['module'] == 'exchange'){
// 							if($arr_acl[$i]['action'] == "index" || $arr_acl[$i]['action'] == "add" || $arr_acl[$i]['action'] == "edited" ) {
// 								continue;
// 							}
// 							$arr_actin[$a_i++] = $arr_acl[$i]['action'];
// 						}
// 					}					
// 					$arr_module=$this->sortMenu($arr_module);
					
// 					$session_user->arr_acl = $arr_acl;
// 					$session_user->arr_module = $arr_module;
// 					$session_user->arr_actin = $arr_actin;
						
					$session_user->lock();
					$log=new Application_Model_DbTable_DbUserLog();
					$log->insertLogin($user_id);
					foreach ($arr_module AS $i => $d){
// 						if($d !== 'user'){
// 							$url = '/' . $arr_module[2];
// 						}
// 						else{
							$url = self::REDIRECT_URL;
							break;
// 						}
					}	
					Application_Form_FrmMessage::redirectUrl("/home");	
					exit();										
				}
				else{					
					$this->view->msg = 'ឈ្មោះ​អ្នក​ប្រើ​ប្រាស់ និង ពាក្យ​​សំងាត់ មិន​ត្រឺម​ត្រូវ​ទេ ';
				}	
			}
			else{				
				$this->view->msg = 'លោកអ្នកមិនមានសិទ្ធិប្រើប្រាស់ទេ!';
			}			
		}		
    }
    public function teacherloginAction()
    {
    	$session_user=new Zend_Session_Namespace('authteacher');
    	if (!empty($session_user->teacher_id)){
    		$this->_redirect("/foundation/teacherscore");
    	}
    	$this->_helper->layout()->disableLayout();
    	$form=new Application_Form_FrmLogin();
    	$form->setAction('index');
    	$form->setMethod('post');
    	$form->setAttrib('accept-charset', 'utf-8');
    	$this->view->form=$form;
    	$key = new Application_Model_DbTable_DbKeycode();
    	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
    
    	if($this->getRequest()->isPost())
    	{
    		$formdata=$this->getRequest()->getPost();
    			
    		if($form->isValid($formdata))
    		{
    			$session_lang=new Zend_Session_Namespace('lang');
    			$session_lang->lang_id=$formdata["lang"];//for creat session
    			Application_Form_FrmLanguages::getCurrentlanguage($formdata["lang"]);//for choose lang for when login
    			$user_name=$form->getValue('txt_user_name');
    			$password=$form->getValue('txt_password');
    			$db_user=new Global_Model_DbTable_DbTeacher();
    			if($db_user->userAuthenticate($user_name,$password)){
    				
    				$user_id=$db_user->getTeacherid($user_name);
    				$user_info = $db_user->getTeacherInfo($user_id);
    					
    				$session_user->teacher_id=$user_id;
    				$session_user->teacher_name=$user_info['teacher_name_en'];
    				$session_user->branch_id= $user_info['branch_id'];
    				$session_user->lock();
//     				$session_user->pwd=$password;
//     				$session_user->level= $user_info['user_type'];
//     				$session_user->last_name= $user_info['last_name'];
//     				$session_user->first_name= $user_info['first_name'];
//     				$session_user->branch_id= $user_info['branch_id'];
//     				$log=new Application_Model_DbTable_DbUserLog();
//     				$log->insertLogin($user_id);
//     				foreach ($arr_module AS $i => $d){
//     					$url = self::REDIRECT_URL;
//     					break;
//     				}
    				Application_Form_FrmMessage::redirectUrl("/foundation/teacherscore");
    				exit();
    			}
    			else{
    				$this->view->msg = 'ឈ្មោះ​អ្នក​ប្រើ​ប្រាស់ និង ពាក្យ​​សំងាត់ មិន​ត្រឺម​ត្រូវ​ទេ ';
    			}
    		}
    		else{
    			$this->view->msg = 'លោកអ្នកមិនមានសិទ្ធិប្រើប្រាស់ទេ!';
    		}
    	}
    }
    
    protected function sortMenu($menus){
    	$menus_order = Array ( 'home','registrar','global','foundation','accounting','stock','library','mobileapp','allreport','rsvacl','setting');
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
        if($this->getRequest()->getParam('value')==1){        	
        	$aut=Zend_Auth::getInstance();
        	$aut->clearIdentity();        	
        	$session_user=new Zend_Session_Namespace('authstu');
        	if(!empty($session_user->user_id)){
	        	$log=new Application_Model_DbTable_DbUserLog();
				$log->insertLogout($session_user->user_id);
	        	$session_user->unsetAll();       	
	        	Application_Form_FrmMessage::redirectUrl("/");
	        	exit();
        	}else{
        		$session_teacher=new Zend_Session_Namespace('authteacher');
        		$session_teacher->teacher_id;
        		$session_teacher->unsetAll();
        		Application_Form_FrmMessage::redirectUrl("/index/teacherlogin");
        		exit();
        	}
        } 
    }

    public function changepasswordAction()
    {
        // action body
        if ($this->getRequest()->isPost()){ 
			$session_user=new Zend_Session_Namespace('authstu');    		
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
    public function  dashboardAction(){
    	$this->_helper->layout()->disableLayout();
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


}





