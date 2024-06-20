<?php 
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
require dirname( dirname(__FILE__) ).'/include/eventmania.php';

header('Content-type: text/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$data = json_decode(file_get_contents('php://input'), true);
function generate_random()
{
	require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
	$six_digit_random_number = mt_rand(100000, 999999);
}

if($data['name'] == ''  or $data['mobile'] == ''   or $data['password'] == '' or $data['ccode'] == '' or $data['email'] == '')
{
    $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went Wrong!");
}
else
{
    $name = strip_tags(mysqli_real_escape_string($event,$data['name']));
	$email = strip_tags(mysqli_real_escape_string($event,$data['email']));
    $mobile = strip_tags(mysqli_real_escape_string($event,$data['mobile']));
	$ccode = strip_tags(mysqli_real_escape_string($event,$data['ccode']));
     $password = strip_tags(mysqli_real_escape_string($event,$data['password']));
    $checkmob = $event->query("select * from admin where mobile=".$mobile."");
  
    if($checkmob->num_rows != 0){
        $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Mobile Number Already Used!");
    }
    else
    {
		   $timestamp = date("Y-m-d H:i:s");
		   $prentcode = generate_random();
		   $table="admin";
  $field_values=array("username","email","mobile","rdate","password","ccode","status");
  $data_values=array("$name","$email","$mobile","$timestamp","$password","$ccode","1");
        $h = new Eventmania();
	  $check = $h->eventinsertdata_Api_Id($field_values,$data_values,$table);
  $c = $event->query("select * from admin where mobile='".$mobile."'  and password='".$password."'");
    $c = $c->fetch_assoc();
  $returnArr = array("UserLogin"=>$c,"ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Sign Up Done Successfully!");   
}
}

echo json_encode($returnArr);