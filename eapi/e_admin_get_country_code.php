<?php
require dirname(dirname(__FILE__)) . '/include/eventconfig.php';
header('Content-type: text/json');
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
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
    echo json_encode(array("ResponseCode" => "404", "Result" => "false", "ResponseMsg" => "Category not found"));
    return;
}

if ($uid == '' or checkAdmin($uid) <= 0) {
    echo json_encode(array("ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Unauthorized"));
    return;
}
$per_page = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 0;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$response_data = [];
$sql = "SELECT * FROM tbl_code WHERE id = '$country_code_id'";

$country_code = [];
$result = $event->query($sql);
while ($row = $result->fetch_assoc()) {
    $country_code = [
        'id' => $row['id'],
        'ccode' => $row['ccode'],
        'status' => $row['status']
    ];
}

$response_data['ResponseCode'] = "200";
$response_data['Result'] = "true";
$response_data['ResponseMsg'] = "";
$response_data['country_code'] = $country_code;
echo json_encode($response_data);
