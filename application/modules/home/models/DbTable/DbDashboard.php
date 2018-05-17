<?php

class Home_Model_DbTable_DbDashboard extends Zend_Db_Table_Abstract
{

    protected $_name = 'tbl_drivers';
	
	
	function getTotalBooking(){
		$db = $this->getAdapter();
		$sql="SELECT COUNT(b.`id`) AS totalBooking FROM `tbl_bookings` AS b";
		return $db->fetchOne($sql);
	}
	function getTotalDriver(){
		$db = $this->getAdapter();
		$sql="SELECT COUNT(b.`id`) AS totalDriver FROM `tbl_drivers` AS b";
		return $db->fetchOne($sql);
	}
	function getTotalCustomer(){
		$db = $this->getAdapter();
		$sql="SELECT COUNT(b.`id`) AS totalCustomer FROM `tbl_passengers` AS b";
		return $db->fetchOne($sql);
	}
	
	
	function getBookingByBackOfficeByYear($year){
		$db = $this->getAdapter();
		$sql="SELECT COUNT(b.`id`) AS total FROM `tbl_bookings` AS b 
			WHERE b.`platform`='back-office' AND DATE_FORMAT(b.`bookingDate`, '%Y') ='$year'";
		return $db->fetchOne($sql);
	}
	function getBookingByAppByYear($year){
		$db = $this->getAdapter();
		$sql="SELECT COUNT(b.`id`) AS total FROM `tbl_bookings` AS b
		WHERE b.`platform`!='back-office' AND DATE_FORMAT(b.`bookingDate`, '%Y') ='$year'";
		return $db->fetchOne($sql);
	}
	
	
	
	
}

