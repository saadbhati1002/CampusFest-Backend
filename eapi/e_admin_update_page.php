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

$page_id = isset($_GET['page_id']) ? $_GET['page_id'] : '';
if ($page_id == '') {
    echo json_encode(array("ResponseCode" => "404", "Result" => "false", "ResponseMsg" => "faq not found"));
    return;
}

$data = json_decode(file_get_contents('php://input'), true);


// if ($uid == '' or checkAdmin($uid) <= 0) {
//     echo json_encode(array("ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Unauthorized"));
//     return;
// }

$required_fields = ['title', 'status'];

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
$table_name = "tbl_page";
$fields = [
     'title','description', 'status'
];

$category_data = [
    'title' => $event->real_escape_string($data['title']),
    'description' => $event->real_escape_string($data['description']),
    'status' => $event->real_escape_string($data['status']),
];

try {

    $eventmedia = new Eventmania();
    $result = $eventmedia->eventupdateData_Api($category_data, $table_name, "where id = $page_id");
    if ($result) {
        echo json_encode(array("ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "Page has been updated successfully."));
    }
} catch (Exception $e) {
    echo json_encode(array("ResponseCode" => "400", "Result" => "false", "ResponseMsg" => $e->getMessage()));
}
