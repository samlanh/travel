<?php
class Home_indexController extends Zend_Controller_Action {
	
	
public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
	}
	public function indexAction()
	{
		$session_user=new Zend_Session_Namespace('auth_travel');
		if (empty($session_user->user_id)){
			$this->_redirect("/");
		}
		$db = new Home_Model_DbTable_DbDashboard();
		$this->view->totalBooking = $db->getTotalBooking();
		$this->view->totalDriver = $db->getTotalDriver();
		$this->view->totalCustomer = $db->getTotalCustomer();
		
	}
	
}

