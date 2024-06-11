<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
  $event = new mysqli("localhost", "u256241708_event", "S@ad1002anam", "u256241708_event");
  $event->set_charset("utf8mb4");
} catch(Exception $e) {
  error_log($e->getMessage());
  //Should be a message a typical user could understand
}
    
$set = $event->query("SELECT * FROM `tbl_setting`")->fetch_assoc();
date_default_timezone_set($set['timezone']);
	
$validate = $event->query("SELECT * FROM `tbl_validate`")->fetch_assoc();
	
?>