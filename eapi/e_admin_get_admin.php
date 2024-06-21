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
$sql = "SELECT * FROM admin";

if ($search != '') {
    $sql .= " WHERE name LIKE '%$search%' OR email LIKE '%$search%' OR mobile LIKE '%$search%'";
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

$users = [];
$result = $event->query($sql);
while ($row = $result->fetch_assoc()) {
    $user = [
        'id' => $row['id'],
        'name' => $row['username'],
        'email' => $row['email'],
        'mobile' => $row['mobile'],
        'rdate' => date("M d Y h:i A", strtotime($row['rdate'])),
        'ccode' => $row['ccode'],
        'pro_pic' =>  $row['pro_pic'],
        'status' => $row['status'] == 1 ? "Active" : "Deactive",
    ];
    array_push($users, $user);
}

$response_data['ResponseCode'] = "200";
$response_data['Result'] = "true";
$response_data['ResponseMsg'] = "true";
$response_data['users'] = $users;
echo json_encode($response_data);
