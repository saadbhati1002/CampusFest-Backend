<?php 
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
if($data['mobile'] == '')
{
    $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went Wrong!");
}
else
{
    $mobile = strip_tags(mysqli_real_escape_string($event,$data['mobile']));
    
    
    
$chek = $event->query("select * from admin where mobile='".$mobile."'")->num_rows;


if($chek != 0)
{
	$returnArr = array("ResponseCode"=>"200","Result"=>"false","ResponseMsg"=>"Account exist with the mobile number");
}
else 
{
	$returnArr = array("ResponseCode"=>"401","Result"=>"true","ResponseMsg"=>"Mobile number not exsist");
}
}
echo json_encode($returnArr);