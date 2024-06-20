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

if ($uid == '' or checkAdmin($uid) <= 0) {
    echo json_encode(array("ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Unauthorized"));
    return;
}
$per_page = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 0;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$response_data = [];
$sql = "SELECT * FROM tbl_event";

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
        'title' => $row['title'],
        'sdate' => date("Y-m-d", strtotime($row['sdate'])),
        'img' => $row['img'],
        'cover_img' =>  $row['cover_img'],
        'status' => $row['status'] == 1 ? "Publish" : "Unpublish",
        'event_status' => $row['event_status'],
        'event_time' => date("g:i A", strtotime($row['stime'])) . ' to ' . date("g:i A", strtotime($row['etime'])),
        'total_tickets' => $event->query("select sum(tlimit) as total_ticket from tbl_type_price where eid=" . $row['id'] . "")->fetch_assoc()['total_ticket'] ?? "0"
    ];
    array_push($events, $event_data);
}

$response_data['ResponseCode'] = "200";
$response_data['Result'] = "true";
$response_data['ResponseMsg'] = "true";
$response_data['events'] = $events;
echo json_encode($response_data);
