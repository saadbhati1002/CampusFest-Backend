<?php
require dirname(dirname(__FILE__)) . '/include/eventmania.php';

header('Content-type: text/json');
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(array("ResponseCode" => "405", "Result" => "false", "ResponseMsg" => "Method not allowed"));
    return;
}

$uid = isset($_SERVER['HTTP_UID']) ? $_SERVER['HTTP_UID'] : '';
if ($uid == '') {
    echo json_encode(array("ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Unauthorized"));
    return;
}

$uid = isset($_GET['uid']) ? $_GET['uid'] : '';
$data = json_decode(file_get_contents('php://input'), true);


// if ($uid == '' or checkAdmin($uid) <= 0) {
//     echo json_encode(array("ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Unauthorized"));
//     return;
// }

$required_fields = [   'ctitle', 'subtitle',"c_title","cdate","c_desc","c_value","min_amt", 'status', 'c_img',];

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
$table_name = "tbl_coupon";
$fields = [
   'ctitle', 'subtitle',"c_title","cdate","c_desc","c_value","min_amt", 'status', 'c_img',
];

$category_data = [
     'title' => $event->real_escape_string($data['title']),
    'subtitle' => $event->real_escape_string($data['subtitle']),
    'c_title' => $event->real_escape_string($data['c_title']),
    'date' => $event->real_escape_string($data['date']),
    'description' => $event->real_escape_string($data['description']),
    'amount' => $event->real_escape_string($data['amount']),
    'minium_amount' => $event->real_escape_string($data['minium_amount']),
    'status' => $event->real_escape_string($data['status']),
];

$img = str_replace(['data:image/png;base64,', 'data:image/jpg;base64,', 'data:image/jpeg;base64,'], '', $data['img']);
$img = str_replace(' ', '+', $data['img']);
$img_content = base64_decode($img);
$path = 'images/category/' . uniqid() . '.png';
$fname = dirname(dirname(__FILE__)) . '/' . $path;
file_put_contents($fname, $img_content);
$category_data['img'] = $path;





try {

    $eventmedia = new Eventmania();
    $result = $eventmedia->eventinsertdata_Api($fields, $category_data, $table_name);
    if ($result) {
        echo json_encode(array("ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "Coupon has been created successfully."));
    }
} catch (Exception $e) {
    echo json_encode(array("ResponseCode" => "400", "Result" => "false", "ResponseMsg" => $e->getMessage()));
    return;
}



