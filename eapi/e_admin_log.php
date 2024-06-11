<?php 
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
if($data['email'] == ''  or $data['password'] == '')
{
    $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went Wrong!");
}
else
{
    $email = strip_tags(mysqli_real_escape_string($event,$data['email']));
    $password = strip_tags(mysqli_real_escape_string($event,$data['password']));
    
$chek = $event->query("select * from admin where email='".$email."'  and password='".$password."'");

if($chek->num_rows != 0)
{
    $c = $event->query("select * from admin where email='".$email."'   and password='".$password."'");
    $c = $c->fetch_assoc();

	
    $returnArr = array("AdminLogin"=>$c,"currency"=>$set['currency'],"ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Login successfully!");
}
else
{
    $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Invalid Email/Mobile No or Password!!!");
}
}

echo json_encode($returnArr);