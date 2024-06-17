<?php
require dirname(dirname(__FILE__)) . '/include/eventmania.php';

header('Content-type: text/json');
if ($_SERVER['REQUEST_METHOD'] != 'PUT') {
    echo json_encode(array("ResponseCode" => "405", "Result" => "false", "ResponseMsg" => "Method not allowed"));
    return;
}

$uid = isset($_SERVER['HTTP_UID']) ? $_SERVER['HTTP_UID'] : '';
if ($uid == '') {
    echo json_encode(array("ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Unauthorized"));
    return;
}

$country_code_id = isset($_GET['country_code_id']) ? $_GET['country_code_id'] : '';
if ($country_code_id == '') {
    echo json_encode(array("ResponseCode" => "404", "Result" => "false", "ResponseMsg" => "Country code not found"));
    return;
}

$data = json_decode(file_get_contents('php://input'), true);


if ($uid == '' or checkAdmin($uid) <= 0) {
    echo json_encode(array("ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Unauthorized"));
    return;
}

$required_fields = ['ccode', 'status'];

$validation_errors = [];
foreach ($required_fields as $field) {
    if (!array_key_exists($field, $data)) {
        array_push($validation_errors, "The $field is required.");
    }
}

if (count($validation_errors)) {
    echo json_encode(array("ResponseCode" => "422", "Result" => "false", "ResponseMsg" => "Invalid request", 'validation_errors' => $validation_errors));
    return;
}
$table_name = "tbl_code";
$fields = [
    'ccode', 'status'
];


$country_code_data = [
    'ccode' => $data['ccode'],
    'status' => $data['status']
];


try {

    $eventmedia = new Eventmania();
    $result = $eventmedia->eventupdateData_Api($country_code_data, $table_name, "where id = $country_code_id");
    if ($result) {
        echo json_encode(array("ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "Country code has been updated successfully."));
    }
} catch (Exception $e) {
    echo json_encode(array("ResponseCode" => "400", "Result" => "false", "ResponseMsg" => $e->getMessage()));
}
