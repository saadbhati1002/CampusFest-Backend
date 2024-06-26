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

// if ($uid == '' or checkAdmin($uid) <= 0) {
//     echo json_encode(array("ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Unauthorized"));
//     return;
// }
$event_id = isset($_GET['event_id']) ? $_GET['event_id'] : '';
if ($event_id == '') {
    echo json_encode(array("ResponseCode" => "404", "Result" => "false", "ResponseMsg" => "Event not found."));
    return;
}
$per_page = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 0;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$response_data = [];
$sql = "SELECT tbl_type_price.*,tbl_event.title as event_name FROM tbl_type_price JOIN tbl_event ON tbl_type_price.eid = tbl_event.id WHERE tbl_type_price.eid = '$event_id'";

if ($search != '') {
    $sql .= " WHERE title LIKE '%$search%'";
}

if ($page > 0) {
    $total_record = $event->query($sql)->num_rows;
    $response_data['pagination'] = [
        'total' => $total_record,
        'current_page' => $page
    ];
    $offset = ($page - 1) * $per_page;
    $sql .= " LIMIT $per_page OFFSET $offset";
}

$events = [];
$result = $event->query($sql);

while ($row = $result->fetch_assoc()) {
    $event_data = [
        'id' => $row['id'],
        'event_name' => $row['event_name'],
        'status' => $row['status'] == 1 ? "Publish" : "Unpublish",
        'price' => $row['price'],
        'tlimit' => $row['tlimit'],
    ];
    array_push($events, $event_data);
}

$response_data['ResponseCode'] = "200";
$response_data['Result'] = "true";
$response_data['ResponseMsg'] = "true";
$response_data['ticket_types'] = $events;
echo json_encode($response_data);
