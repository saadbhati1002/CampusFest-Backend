<?php 
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
require dirname( dirname(__FILE__) ).'/include/eventmania.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
if($data['uid'] == '')
{
 $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went Wrong!");    
}
else
{
	  $uid = $data['uid'];  
   $table = "admin";
if($data['status']==0){
    $field = "status=0";
}else{
    $field = "status=1";
}
            

            $where = "where id=" . $uid . "";

            $h = new Eventmania();
           
            $check = $h->eventupdateData_single($field, $table, $where);
			
 $returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Account status changed Successfully!!");
}
echo  json_encode($returnArr);
?>