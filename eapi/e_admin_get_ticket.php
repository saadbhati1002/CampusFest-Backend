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
$per_page = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 0;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$response_data = [];
$sql = "SELECT * FROM tbl_ticket";

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

$categories = [];
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
$response_data['tickets'] = $categories;
echo json_encode($response_data);
