<?php
class Report_indexController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
  function indexAction(){
  	if($this->getRequest()->isPost()){
  		$search=$this->getRequest()->getPost();
  	}
  	else{
  		$search = array(
  				'start'=> date('Y-m-d'),
  				'end'=>date('Y-m-d')
  		);
  	}
  	$this->view->search = $search;
  	$db = new Report_Model_DbTable_DbBooking();
  	$this->view->allbooking = $db->getAllBooking($search);
  	
  	$this->view->totalbooking = $db->getTotalBooking($search);
  	$this->view->backoffice = $db->getBookingByBackOffice($search);
  	$this->view->appbooking = $db->getBookingByApp($search);
  	$this->view->bookingcancel = $db->getBookingCancel($search);
  }
  
}

