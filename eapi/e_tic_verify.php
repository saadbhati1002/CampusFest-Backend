<?php 
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
require dirname( dirname(__FILE__) ).'/include/eventmania.php';

$data = json_decode(file_get_contents('php://input'), true);

if($data['uid'] == ''  or $data['tic_id'] == '' or $data['ve_date'] == '')
{
    $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went Wrong!");
}
else
{
    $uid = strip_tags(mysqli_real_escape_string($event,$data['uid']));
    $tic_id = strip_tags(mysqli_real_escape_string($event,$data['tic_id']));
    $ve_date = strip_tags(mysqli_real_escape_string($event,$data['ve_date']));
    
    
    $get_tic = $event->query("select * from tbl_ticket where id=".$tic_id." and uid=".$uid."");
     if($get_tic->num_rows == 0)
    {
        $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Ticket Not Found!!");
    }
    else 
    {
    $rec = $get_tic->fetch_assoc();
    $edata = $event->query("select * from tbl_event where id=".$rec['eid']."")->fetch_assoc();
    if($rec['ticket_type'] == 'Cancelled')
    {
       $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Ticket Already Cancelled!!"); 
    }
    else if($edata['sdate'] != $data['ve_date'])
    {
       $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Ticket Date Not Matched With Current Date!!");  
    }
    else if($rec['is_verify'] == '1')
    {
       $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Ticket Already Verified!!");  
    }
    else 
    {
        $table="tbl_ticket";
  $field = array('is_verify'=>'1');
  $where = "where uid=".$uid." and id=".$tic_id."";
$h = new Eventmania();
//$check = $h->eventupdateData_Api($field,$table,$where);
$returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"For Demo purpose all  Insert/Update/Delete are DISABLED !!");  
    }
}
}

echo json_encode($returnArr);