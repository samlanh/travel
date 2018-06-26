<?php

class Other_Model_DbTable_DbTestimonial extends Zend_Db_Table_Abstract
{

    protected $_name = 'tp_condition';
    public function getUserId(){
    	$db = new Application_Model_DbTable_DbGlobal();
		return $db->getUserId();   
    } 
    
    static function getCurrentLang(){
    	$session_lang=new Zend_Session_Namespace('lang');
    	if(!empty($session_lang->lang_id)){
    		if ($session_lang->lang_id>2){
    			return 2;
    		}
    		return $session_lang->lang_id;
    	}else{
    		return 2;
    	}
    }
    
	 function getAllTestimonial($search=null){
		   	$db = $this->getAdapter();
		   	$db->beginTransaction();
		   	try{
		   		
		   		$sql="SELECT id,`person_naem`,DATE_FORMAT(`createDate`, '%d-%M-%Y'),DATE_FORMAT(`modifyDate`,'%d-%M-%Y'),`description`,
                        (SELECT v.name_en FROM `tp_view` AS v WHERE v.key_code=tp_testimonial.`status` AND v.type=1 LIMIT 1) AS `status`
                        FROM `tp_testimonial`
                        WHERE `description`!=''";
		   		$where="";
		   		$from_date =(empty($search['start']))? '1': " createDate >= '".date("Y-m-d",strtotime($search['start']))." 00:00:00'";
		   		$to_date = (empty($search['end']))? '1': " createDate <= '".date("Y-m-d",strtotime($search['end']))." 23:59:59'";
		   		$where.= " AND  ".$from_date." AND ".$to_date;
		   		if(!empty($search['adv_search'])){
		   			$s_where = array();
		   			$s_search = addslashes(trim($search['adv_search']));
		   			$s_search = str_replace(' ', '', $s_search);
		   			$s_where[]="REPLACE(description,' ','')   LIKE '%{$s_search}%'";
		   			$s_where[]="REPLACE(person_naem,' ','')   LIKE '%{$s_search}%'";
		   			$where .=' AND ('.implode(' OR ',$s_where).')';
		   		}
		   		
		   		$from_date =(empty($search['start']))? '1': " `createDate` >= '".date("Y-m-d",strtotime($search['start']))." 00:00:00'";
		   		$to_date = (empty($search['end']))? '1': " `createDate` <= '".date("Y-m-d",strtotime($search['end']))." 23:59:59'";
		   		$where="";
		   		$where.= " AND  ".$from_date." AND ".$to_date;
		   		if($search['status']>-1){
		   			$where.=" AND status=".$search['status'];
		   		}
		   		$order=" AND type=1 ORDER BY id DESC";
		   		return $db->fetchAll($sql.$where.$order);
	   		}catch(exception $e){
	   			Application_Form_FrmMessage::message("Application Error");
	   			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
	   			$db->rollBack();
	   		}
   	}
   	
   	function addTestimonial($_data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    	    $valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
    	    $part= PUBLIC_PATH.'/images/all/';
    	    if (!file_exists($part)) {
    	        mkdir($part, 0777, true);
    	    }
    	    $image_name = "";
    	    $photo="";
    	    $name = $_FILES['photo']['name'];
    	    if (!empty($name)){
    	        $ss = explode(".", $name);
    	        $image_name = "p".date("Y").date("m").date("d").time().".".end($ss);
    	        $tmp = $_FILES['photo']['tmp_name'];
    	        if(move_uploaded_file($tmp, $part.$image_name)){
    	            $photo = $image_name;
    	        }else{
    	            $photo = "Image Upload failed";
    	        }
    	    }
    	    
    		$_arr=array(
    		    'person_naem'    => $_data['name_person'],
    		    'positon'        => $_data['position'],
    		    'description'    => $_data['description'],
    			'createDate'	 => date("Y-m-d H:i:s"),
    			'modifyDate'	 => date("Y-m-d H:i:s"),
    			'orderBy'		 => $_data['order_by'],	
    		    'image'          => $photo,
    			'status'         => 1,
    		    'type'           => 1,
    			'user_id'        => $this->getUserId(),
    		);
    		$this->_name="tp_testimonial";
    		$pro_id =$this->insert($_arr);
    		$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
    }
    
    function editTestimonial($_data){
        $db = $this->getAdapter();
        $db->beginTransaction();
        try{
            $valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
            $part= PUBLIC_PATH.'/images/all/';
            if (!file_exists($part)) {
                mkdir($part, 0777, true);
            }
            $image_name = "";
            $photo="";
            $name = $_FILES['photo']['name'];
            if (!empty($name)){
                $ss = explode(".", $name);
                $image_name = "p".date("Y").date("m").date("d").time().".".end($ss);
                $tmp = $_FILES['photo']['tmp_name'];
                if(move_uploaded_file($tmp, $part.$image_name)){
                    $photo = $image_name;
                }else{
                    $photo = $_data['old_pic'];
                }
            }else{
                $photo = $_data['old_pic'];
            }
            
            $_arr=array(
                'person_naem'    => $_data['name_person'],
                'positon'        => $_data['position'],
                'description'    => $_data['description'],
                'modifyDate'	 => date("Y-m-d H:i:s"),
                'orderBy'		 => $_data['order_by'],
                'image'          => $photo,
                'status'         => $_data['status'],
                'type'           => 1,
                'user_id'        => $this->getUserId(),
            );
            $this->_name="tp_testimonial";
            $where=" id=".$_data['id'];
            $this->update($_arr, $where);
            $db->commit();
        }catch(exception $e){
            Application_Form_FrmMessage::message("Application Error");
            Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
            $db->rollBack();
        }
    }
    
	function getAllService(){
		$lang= $this->getCurrentLang();
		$array = array(1=>"province_en_name",2=>"province_kh_name");
		$db = $this->getAdapter();
		$sql="  SELECT id,`serviceName` AS `name` FROM `tp_location_service` WHERE `status`=1";
		return $db->fetchAll($sql);
	}
	
	function getTermsById($id){
		$db = $this->getAdapter();
		$sql=" SELECT * FROM `tp_testimonial` WHERE `type`=1 AND id=$id";
		return $db->fetchRow($sql);
	}
	
}

