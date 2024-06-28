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
    echo json_encode(array("ResponseCode" => "404", "Result" => "false", "ResponseMsg" => "event not found"));
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
$sql = "SELECT * FROM tbl_ticket WHERE eid = '$event_id'";

$category = [];
$result = $event->query($sql);
while ($row = $result->fetch_assoc()) {
    $event_id=$row['eid'];
$sql1 = "SELECT * FROM tbl_event WHERE id = '$event_id'";
$result1 = $event->query($sql1);
    $user_id=$row['uid'];
$sql2 = "SELECT * FROM tbl_user WHERE id = '$user_id'";
$result2 = $event->query($sql2);


while ($row1 = $result1->fetch_assoc()) {
    while ($row2 = $result2->fetch_assoc()) {
    $category = [
        'id' => $row['id'],
         'event_name' => $row1['title'],
        'user_name' => $row2['name'],
        'user_id' => $row2['id'],
        'price' => $row['price'],
        'total_ticket' => $row['total_ticket'],
        'ticket_type' => $row['ticket_type'] ,
    ];
    array_push($categories, $category);
}
}
}
$response_data['ResponseCode'] = "200";
$response_data['Result'] = "true";
$response_data['ResponseMsg'] = "true";
$response_data['tickets'] = $category;
echo json_encode($response_data);
