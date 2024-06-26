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

$data = json_decode(file_get_contents('php://input'), true) ?? [];


// if ($uid == '' or checkAdmin($uid) <= 0) {
//     echo json_encode(array("ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Unauthorized"));
//     return;
// }

$required_fields = ['cid', 'title', 'img', 'cover_img', 'sdate', 'stime', 'etime', 'latitude', 'longtitude', 'place_name', 'status', 'address', 'description', 'disclaimer'];

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
$table_name = "tbl_event";
$fields = [
    'cid', 'title', 'sdate', 'stime', 'etime', 'latitude', 'longtitude', 'place_name', 'status', 'address', 'description', 'disclaimer', 'img', 'cover_img'
];

$category_data = [
    'cid' => $data['cid'],
    'title' => $data['title'],
    'sdate' => $data['sdate'],
    'stime' => $data['stime'],
    'etime' => $data['etime'],
    'latitude' => $data['latitude'],
    'longtitude' => $data['longtitude'],
    'place_name' => $data['place_name'],
    'status' => $data['status'],
    'address' => $data['address'],
    'description' => $data['description'],
    'disclaimer' => $data['disclaimer'],
];

$img = str_replace(['data:image/png;base64,', 'data:image/jpg;base64,', 'data:image/jpeg;base64,'], '', $data['img']);
$img = str_replace(' ', '+', $data['img']);
$img_content = base64_decode($img);
$path = 'images/event/' . uniqid() . '.png';
$fname = dirname(dirname(__FILE__)) . '/' . $path;
file_put_contents($fname, $img_content);
$category_data['img'] = $path;



$cover_img = str_replace(['data:image/png;base64,', 'data:image/jpg;base64,', 'data:image/jpeg;base64,'], '', $data['cover_img']);
$cover_img = str_replace(' ', '+', $data['cover_img']);
$cover_img_content = base64_decode($img);
$path = 'images/event/' . uniqid() . '.png';
$fname = dirname(dirname(__FILE__)) . '/' . $path;
file_put_contents($fname, $cover_img_content);
$category_data['cover_img'] = $path;

try {

    $eventmedia = new Eventmania();
    $result = $eventmedia->eventinsertdata_Api($fields, $category_data, $table_name);
    if ($result) {
        echo json_encode(array("ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "Event has been added successfully."));
    }
} catch (Exception $e) {
    echo json_encode(array("ResponseCode" => "400", "Result" => "false", "ResponseMsg" => $e->getMessage()));
    return;
}
