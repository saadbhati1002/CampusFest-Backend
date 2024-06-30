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

$price_type_id = isset($_GET['price_type_id']) ? $_GET['price_type_id'] : '';
if ($price_type_id == '') {
    echo json_encode(array("ResponseCode" => "404", "Result" => "false", "ResponseMsg" => "id not found"));
    return;
}

$data = json_decode(file_get_contents('php://input'), true);


// if ($uid == '' or checkAdmin($uid) <= 0) {
//     echo json_encode(array("ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Unauthorized"));
//     return;
// }

$required_fields = [    'eid','type', 'price',"tlimit","status"
];

// $validation_errors = [];
// foreach ($required_fields as $field) {
//     if (!array_key_exists($field, $data)) {
//         array_push($validation_errors, "The $field is required.");
//     }
// }

// if (count($validation_errors)) {
//     echo json_encode(array("ResponseCode" => "422", "Result" => "false", "ResponseMsg" => "Invalid request", 'validation_errors' => $validation_errors));
//     return;
// }
$table_name = "tbl_type_price";
$fields = [
     'eid','type', 'price',"tlimit","status"

];

$category_data = [
    'eid' => $event->real_escape_string($data['eid']),
    'type' => $event->real_escape_string($data['type']),
    'price' => $event->real_escape_string($data['price']),
    'tlimit' => $event->real_escape_string($data['tlimit']),
    'status' => $event->real_escape_string($data['status']),
];

try {

    $eventmedia = new Eventmania();
    $result = $eventmedia->eventupdateData_Api($category_data, $table_name, "where id = $price_type_id");
    if ($result) {
        echo json_encode(array("ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "Price & Type has been updated successfully."));
    }
} catch (Exception $e) {
    echo json_encode(array("ResponseCode" => "400", "Result" => "false", "ResponseMsg" => $e->getMessage()));
}
