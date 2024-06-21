<?php
require dirname(dirname(__FILE__)) . '/include/eventmania.php';

header('Content-type: text/json');
if ($_SERVER['REQUEST_METHOD'] != 'DELETE') {
    echo json_encode(array("ResponseCode" => "405", "Result" => "false", "ResponseMsg" => "Method not allowed"));
    return;
}

$uid = isset($_SERVER['HTTP_UID']) ? $_SERVER['HTTP_UID'] : '';
if ($uid == '') {
    echo json_encode(array("ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Unauthorized"));
    return;
}

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';
if ($user_id == '') {
    echo json_encode(array("ResponseCode" => "404", "Result" => "false", "ResponseMsg" => "User ID not found"));
    return;
}

$data = json_decode(file_get_contents('php://input'), true);


// if ($uid == '' or checkAdmin($uid) <= 0) {
//     echo json_encode(array("ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Unauthorized"));
//     return;
// }



$table_name = "tbl_user";

try {

    $eventmedia = new Eventmania();
    $result = $eventmedia->eventDeleteData("where id = $user_id", $table_name);
    if ($result) {
        echo json_encode(array("ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "User account deleted successfully."));
    }
} catch (Exception $e) {
    echo json_encode(array("ResponseCode" => "400", "Result" => "false", "ResponseMsg" => $e->getMessage()));
}
