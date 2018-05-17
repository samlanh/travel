<?php

class Report_Model_DbTable_DbBooking extends Zend_Db_Table_Abstract
{

    protected $_name = 'tbl_bookings';
	function getAllBooking($search=NULL){
		   	$db = $this->getAdapter();
		   	$db->beginTransaction();
		   	try{
		   		$from_date =(empty($search['start']))? '1': " b.`bookingDate` >= '".date("Y-m-d",strtotime($search['start']))." 00:00:00'";
		   		$to_date = (empty($search['end']))? '1': " b.`bookingDate` <= '".date("Y-m-d",strtotime($search['end']))." 23:59:59'";
		   		
		   		$sql="SELECT b.*,
				(SELECT d.driverId FROM `tbl_drivers` AS d WHERE d.id = b.`driverId` LIMIT 1) AS driverId,
				(SELECT p.phoneNumber FROM `tbl_passengers` AS p WHERE p.id = b.`passengerId` LIMIT 1) AS phoneNumber
		   		FROM `tbl_bookings` AS b WHERE 1";
		   		$where="";
		   		$where.= " AND  ".$from_date." AND ".$to_date;
		   		$order=" ORDER BY b.`bookingDate` DESC";
		   		return $db->fetchAll($sql.$where.$order);
	   		}catch(exception $e){
	   			Application_Form_FrmMessage::message("Application Error");
	   			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
	   			$db->rollBack();
	   		}
   	}
   	
   	function getTotalBooking($search=NULL){
   		$db = $this->getAdapter();
   		$from_date =(empty($search['start']))? '1': " b.`bookingDate` >= '".date("Y-m-d",strtotime($search['start']))." 00:00:00'";
   		$to_date = (empty($search['end']))? '1': " b.`bookingDate` <= '".date("Y-m-d",strtotime($search['end']))." 23:59:59'";
   		
   		$sql="SELECT COUNT(b.`id`) AS totalBooking FROM `tbl_bookings` AS b";
   		$where=" WHERE 1 ";
   		$where.= " AND  ".$from_date." AND ".$to_date;
   		
   		return $db->fetchOne($sql.$where);
   	}
   	function getBookingByBackOffice($search=NULL){
   		$db = $this->getAdapter();
   		
   		$from_date =(empty($search['start']))? '1': " b.`bookingDate` >= '".date("Y-m-d",strtotime($search['start']))." 00:00:00'";
   		$to_date = (empty($search['end']))? '1': " b.`bookingDate` <= '".date("Y-m-d",strtotime($search['end']))." 23:59:59'";
   		
   		$sql="SELECT COUNT(b.`id`) AS total FROM `tbl_bookings` AS b
   		WHERE b.`platform`='back-office' ";
   		$where=" ";
   		$where.= " AND  ".$from_date." AND ".$to_date;
   		return $db->fetchOne($sql.$where);
   	}
   	function getBookingByApp($search=NULL){
	   	$db = $this->getAdapter();
	   	
	   	$from_date =(empty($search['start']))? '1': " b.`bookingDate` >= '".date("Y-m-d",strtotime($search['start']))." 00:00:00'";
	   	$to_date = (empty($search['end']))? '1': " b.`bookingDate` <= '".date("Y-m-d",strtotime($search['end']))." 23:59:59'";
	   	
	   	$sql="SELECT COUNT(b.`id`) AS total FROM `tbl_bookings` AS b
	   	WHERE b.`platform`!='back-office' ";
	
	   	$where=" ";
   		$where.= " AND  ".$from_date." AND ".$to_date;
   		return $db->fetchOne($sql.$where);
   	}
   	
   	function getBookingCancel($search=NULL){
   		$db = $this->getAdapter();
   		 
   		$from_date =(empty($search['start']))? '1': " b.`bookingDate` >= '".date("Y-m-d",strtotime($search['start']))." 00:00:00'";
   		$to_date = (empty($search['end']))? '1': " b.`bookingDate` <= '".date("Y-m-d",strtotime($search['end']))." 23:59:59'";
   		 
   		$sql="SELECT COUNT(b.`id`) AS total FROM `tbl_bookings` AS b
   		WHERE b.`status`='cancelled' ";
   	
   		$where=" ";
   		$where.= " AND  ".$from_date." AND ".$to_date;
   		return $db->fetchOne($sql.$where);
   	}
}

