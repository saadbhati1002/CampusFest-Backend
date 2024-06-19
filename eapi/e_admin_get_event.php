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

$event_id = isset($_GET['event_id']) ? $_GET['event_id'] : '';
if ($event_id == '') {
    echo json_encode(array("ResponseCode" => "404", "Result" => "false", "ResponseMsg" => "Event not found"));
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
$sql = "SELECT * FROM tbl_event WHERE id = '$event_id'";

$event_data = [];
$result = $event->query($sql);
while ($row = $result->fetch_assoc()) {
    $event_data = [
        'id' => $row['id'],
        'title' => $row['title'],
        'img' => $row['img'],
        'cover_img' =>  $row['cover_img'],
        'sdate' =>  $row['sdate'],
        'stime' =>  $row['stime'],
        'etime' =>  $row['etime'],
        'latitude' =>  $row['latitude'],
        'longtitude' =>  $row['longtitude'],
        'place_name' =>  $row['place_name'],
        'status' => $row['status'],
        'adress' => $row['address'],
        'cid' => $row['cid'],
        'description' => $row['description'],
        'disclaimer' => $row['disclaimer'],
    ];
}

$response_data['ResponseCode'] = "200";
$response_data['Result'] = "true";
$response_data['ResponseMsg'] = "true";
$response_data['event'] = $event_data;
echo json_encode($response_data);
