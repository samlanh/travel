<?php
class Vehicle_Model_DbTable_DbVehiclePrice extends Zend_Db_Table_Abstract
{
    protected $_name = 'tp_price';
    
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth_travel');
		return $session_user->user_id;
    }
    
    function getAllVehiclePrice($search=null){
    	$db=$this->getAdapter();
    	$sql=" SELECT
			    	p.id,
			    	s.supplyerName,
			    	v.title,
			    	(select name_en from tp_view as v where v.type=4 and v.key_code = p.isAvailable) as isAvailable,
			    	note,
			    	p.createDate,
			    	(select first_name from rms_users as u where u.id = p.userId) as user,
			    	(select name_en from tp_view as v where v.type=1 and v.key_code = p.status) as status
    			FROM
			    	tp_price as p,
			    	tp_supplier as s,
			    	tp_vehicletype as v
    			WHERE
			    	p.supplyerId = s.id
			    	and p.vehicleType = v.id
    		";
    
    	$where=" ";
    
    	$from_date =(empty($search['start']))? '1': " p.createDate >= '".$search['start']." 00:00:00'";
    	$to_date = (empty($search['end']))? '1': " p.createDate <= '".$search['end']." 23:59:59'";
    	$where = " AND ".$from_date." AND ".$to_date;
    
    	if(!empty($search['adv_search'])){
	    	$s_where=array();
	    	$s_search=addslashes(trim($search['adv_search']));
	    	$s_where[]= " stu_code LIKE '%{$s_search}%'";
	    	$s_where[]=" receipt_number LIKE '%{$s_search}%'";
	    	$s_where[]= " stu_khname LIKE '%{$s_search}%'";
	    	$s_where[]= " stu_enname LIKE '%{$s_search}%'";
	    	$s_where[]= " sp.grade LIKE '%{$s_search}%'";
	    	$where.=' AND ('.implode(' OR ', $s_where).')';
    	}
    	if(!empty($search['user'])){
    		$where.=" AND sp.user_id=".$search['user'];
    	}
    	$order=" ORDER BY p.id DESC";
    	
    	echo $sql.$where.$order;
    	
    	return $db->fetchAll($sql.$where.$order);
    }
    
    function insertPrice($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$_arr=array(
    				'supplyerId'		=> $data['supplyerId'],
    				'vehicleType'	 	=> $data['vehicleType'],
    				'isAvailable'      	=> $data['isAvailable'],
    				'note'      		=> $data['note'],
    				'createDate'	 	=> date("Y-m-d H:i:s"),
    				'modifyDate'	 	=> date("Y-m-d H:i:s"),
    				'status'         	=> 1,
    				'userId'         	=> $this->getUserId(),
    		);
    		$price_id =$this->insert($_arr);
    		
    		if(!empty($data['identity'])){
    			$iden = explode(",", $data['identity']);
    			foreach ($iden as $i){
    				$arra=array(
    					'price_id'		=> $price_id,
    					'from_location'	=> $data['from_location_'.$i],
    					'to_location'	=> $data['to_location_'.$i],
    					'duration'		=> $data['duration_'.$i],
    					'cost'			=> $data['cost_'.$i],
    					'price'			=> $data['price_'.$i],
    					'discount'		=> $data['discount_'.$i],
    				);
    				$this->_name="tp_price_detail";
    				$this->insert($arra);
    			}
    		}
    		
    		$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		echo $e->getMessage();
    		$db->rollBack();
    	}
    }
    
    function updatePrice($data,$id){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$_arr=array(
    				'supplyerId'		=> $data['supplyerId'],
    				'vehicleType'	 	=> $data['vehicleType'],
    				'isAvailable'      	=> $data['isAvailable'],
    				'note'      		=> $data['note'],
    				'modifyDate'	 	=> date("Y-m-d H:i:s"),
    				'status'         	=> 1,
    				'userId'         	=> $this->getUserId(),
    		);
    		$this->_name="tp_price";
    		$where = " id = $id";
    		$this->update($_arr, $where);
    
    		$this->_name="tp_price_detail";
    		$where1 = "price_id = $id";
    		$this->delete($where1);
    		
    		if(!empty($data['identity'])){
    			$iden = explode(",", $data['identity']);
    			foreach ($iden as $i){
    				$arra=array(
    						'price_id'		=> $id,
    						'from_location'	=> $data['from_location_'.$i],
    						'to_location'	=> $data['to_location_'.$i],
    						'duration'		=> $data['duration_'.$i],
    						'cost'			=> $data['cost_'.$i],
    						'price'			=> $data['price_'.$i],
    						'discount'		=> $data['discount_'.$i],
    				);
    				$this->_name="tp_price_detail";
    				$this->insert($arra);
    			}
    		}
    
    		$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		echo $e->getMessage();
    		$db->rollBack();
    	}
    }
    
    function getAllPriceById($id){
    	$db = $this->getAdapter();
    	$sql="SELECT * FROM tp_price where id=$id limit 1";
    	return $db->fetchRow($sql);
    }
    function getAllPriceDetailById($id){
    	$db = $this->getAdapter();
    	$sql="SELECT * FROM tp_price_detail where price_id=$id ";
    	return $db->fetchAll($sql);
    }
    
    function getAllSupplyer(){
    	$db = $this->getAdapter();
    	$sql="SELECT id,supplyerName AS name FROM tp_supplier where supplyerName!='' and status=1 ";
    	return $db->fetchAll($sql);
    }
    
	function getAllLocation(){
		$db = $this->getAdapter();
		$sql="SELECT id,locationName AS name FROM tp_locations where locationName!='' and status=1 ";
		return $db->fetchAll($sql);
	}
	
	function getAllVehicleType(){
		$db = $this->getAdapter();
		$sql="SELECT id,title AS name FROM tp_vehicletype where title!='' and status=1 ";
		return $db->fetchAll($sql);
	}
}

