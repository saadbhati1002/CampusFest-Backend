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

$data = json_decode(file_get_contents('php://input'), true);


if ($uid == '' or checkAdmin($uid) <= 0) {
    echo json_encode(array("ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Unauthorized"));
    return;
}

$required_fields = ['name', 'email', 'mobile', 'password', 'status', 'ccode', 'code'];

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
$table_name = "tbl_user";
$fields = ['name', 'email', 'mobile', 'password', 'status', 'ccode', 'code', 'refercode', 'wallet', 'pro_pic'];

$user_data = [
    'name' => $data['name'],
    'email' => $data['email'],
    'mobile' => $data['mobile'],
    'password' => $data['password'],
    'status' => $data['status'],
    'ccode' => $data['ccode'],
    'code' => $data['code'],
    'refercode' => isset($data['refercode']) ? $data['refercode'] : null,
    'wallet' => isset($data['wallet']) ? $data['wallet'] : 0,
    'pro_pic' => null
];

if (isset($data['pro_pic'])) {
    $img = str_replace(['data:image/png;base64,', 'data:image/jpg;base64,', 'data:image/jpeg;base64,'], '', $data['pro_pic']);
    $img = str_replace(' ', '+', $data['pro_pic']);
    $img_content = base64_decode($img);
    $path = 'images/profile/' . uniqid() . '.png';
    $fname = dirname(dirname(__FILE__)) . '/' . $path;
    file_put_contents($fname, $img_content);
    $user_data['pro_pic'] = $path;
}

try {

    $eventmedia = new Eventmania();
    $result = $eventmedia->eventinsertdata_Api($fields, $user_data, $table_name);
    if ($result) {
        echo json_encode(array("ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "User has been added successfully."));
    }
} catch (Exception $e) {
    echo json_encode(array("ResponseCode" => "400", "Result" => "false", "ResponseMsg" => $e->getMessage()));
    return;
}



