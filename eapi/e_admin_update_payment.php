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

$payment_id = isset($_GET['payment_id']) ? $_GET['payment_id'] : '';
if ($payment_id == '') {
    echo json_encode(array("ResponseCode" => "404", "Result" => "false", "ResponseMsg" => "id not found"));
    return;
}

$data = json_decode(file_get_contents('php://input'), true);


// if ($uid == '' or checkAdmin($uid) <= 0) {
//     echo json_encode(array("ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Unauthorized"));
//     return;
// }

$required_fields = [ 'title','subtitle','attributes', 'status','p_show','image'];

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
$table_name = "tbl_payment_list";
$fields = [
     'title','subtitle','attributes', 'status','p_show','image'
];

$category_data = [
    'title' => $event->real_escape_string($data['title']),
    'subtitle' => $event->real_escape_string($data['subtitle']),
    'attributes' => $event->real_escape_string($data['attributes']),
    'status' => $event->real_escape_string($data['status']),
    'p_show' => $event->real_escape_string($data['p_show']),
];
if($data['img']!=""){
if (isset($data['img'])) {
    $img = str_replace(['data:image/png;base64,', 'data:image/jpg;base64,', 'data:image/jpeg;base64,'], '', $data['img']);
    $img = str_replace(' ', '+', $data['img']);
    $img_content = base64_decode($img);
    $path = 'images/category/' . uniqid() . '.png';
    $fname = dirname(dirname(__FILE__)) . '/' . $path;
    file_put_contents($fname, $img_content);
    $category_data['img'] = $path;
    array_push($fields, 'img');
}
}

try {

    $eventmedia = new Eventmania();
    $result = $eventmedia->eventupdateData_Api($category_data, $table_name, "where id = $payment_id");
    if ($result) {
        echo json_encode(array("ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "Payment details has been updated successfully."));
    }
} catch (Exception $e) {
    echo json_encode(array("ResponseCode" => "400", "Result" => "false", "ResponseMsg" => $e->getMessage()));
}
