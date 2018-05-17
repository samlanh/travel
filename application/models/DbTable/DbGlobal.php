<?php

class Application_Model_DbTable_DbGlobal extends Zend_Db_Table_Abstract
{
	public function setName($name){
		$this->_name=$name;
	}
	public static function getUserId(){
		$session_user=new Zend_Session_Namespace('auth_flower');
		return $session_user->user_id;
	}
	public static function GlobalgetUserId(){
		$session_user=new Zend_Session_Namespace('auth_flower');
		return $session_user->user_id;
	}
	function getAllCustomer(){
		return array();
	}
	public function getAccessPermission($branch_str='branch_id'){
		$session_user=new Zend_Session_Namespace('auth_flower');
		$branch_id = $session_user->branch_id;
		$level = $session_user->level;
		if($level==1 OR $level==2){
			$result = "";
			return '';
		}
		else{
			$result = " AND $branch_str =".$branch_id;
			return '';
		}
	}
	
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	
	public function getGlobalDb($sql)
  	{
  		$db=$this->getAdapter();
  		$row=$db->fetchAll($sql);  		
  		if(!$row) return NULL;
  		return $row;
  	}
  	public function getGlobalDbRow($sql)
  	{
  		$db=$this->getAdapter();  		
  		$row=$db->fetchRow($sql);
  		if(!$row) return NULL;
  		return $row;
  	}
  	public static function getActionAccess($action)
    {
    	$arr=explode('-', $action);
    	return $arr[0];    	
    }     
   
    public function getDayInkhmerBystr($str){
    	
    	$rs=array(
    			'Mon'=>'ច័ន្ទ',
    			'Tue'=>'អង្គារ',
    			'Wed'=>'ពុធ',
    			'Thu'=>"ព្រហ",
    			'Fri'=>"សុក្រ",
    			'Sat'=>"សៅរី",
    			'Sun'=>"អាទិត្យ");
    	if($str==null){
    		return $rs;
    	}else{
    	return $rs[$str];
    	}
    
    }
   

 

   function resizeImase($image,$part,$new_imagename=null){
    $photo = $image;
    $temp = explode(".", $photo["name"]);
    $new_name = $temp[0].end($temp);
    if (!empty($new_imagename)){
      $new_name = $new_imagename;
    }
    move_uploaded_file($image["tmp_name"], $part .$new_name);
      
    $uploadimage=$part.$new_name;
//    $newname=$image["name"];
//    // Load the stamp and the photo to apply the watermark to
    if (end($temp) == 'jpg') {
      $im = imagecreatefromjpeg($uploadimage);
    } else
      if (end($temp) == 'jpeg') {
      $im = imagecreatefromjpeg($uploadimage);
    } else
      if (end($temp) == 'png') {
      $im = imagecreatefrompng($uploadimage);
    } else
      if (end($temp) == 'gif') {
      $im = imagecreatefromgif($uploadimage);
    }
  
    if ($image['size']>(1000000*5)){
      // Save the image to file and free memory quality 50%
      imagejpeg($im, $uploadimage, 50);
    }else if($image['size']>(1000000)){
      imagejpeg($im, $uploadimage, 70); //quality 80%
    }else if($image['size']>512000){
      // Save the image to file and free memory quality 60%
      imagejpeg($im, $uploadimage, 80);
    }
  //  imagedestroy($uploadimage);
    return $new_name;
      
  }
   
   
}
?>