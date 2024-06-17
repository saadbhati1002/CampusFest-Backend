<?php
require dirname(dirname(__FILE__)) . '/include/eventmania.php';

header('Content-type: text/json');
if ($_SERVER['REQUEST_METHOD'] != 'PATCH') {
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
    echo json_encode(array("ResponseCode" => "404", "Result" => "false", "ResponseMsg" => "User not found"));
    return;
}

$data = json_decode(file_get_contents('php://input'), true);


if ($uid == '' or checkAdmin($uid) <= 0) {
    echo json_encode(array("ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Unauthorized"));
    return;
}



$table_name = "tbl_user";
$fields = [
    'status'
];


$validation_errors = [];
foreach ($fields as $field) {
    if (!array_key_exists($field, $data)) {
        array_push($validation_errors, "The $field is required.");
    }
}

if (count($validation_errors)) {
    echo json_encode(array("ResponseCode" => "422", "Result" => "false", "ResponseMsg" => "Invalid request", 'validation_errors' => $validation_errors));
    return;
}


try {

    $eventmedia = new Eventmania();
    $result = $eventmedia->eventupdateData_Api(['status' => $data['status']], $table_name, "where id= '$user_id'");
    if ($result) {
        echo json_encode(array("ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "User has been " . ($data['status'] == '1' ? 'activated' : 'deactivated') . " successfully."));
    }
} catch (Exception $e) {
    echo json_encode(array("ResponseCode" => "400", "Result" => "false", "ResponseMsg" => $e->getMessage()));
}
