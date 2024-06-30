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
$sql = "SELECT * FROM tbl_payment_list";

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
    $category = [
        'id' => $row['id'],
        'title' => $row['title'],
        'subtitle' => $row['subtitle'],
        'img' => $row['img'],
        'status' => $row['status'] == 1 ? "Publish" : "Unpublish",
        'p_show' => $row['status'] == 1 ? "Yes" : "No",
    ];
    array_push($categories, $category);

}

$response_data['ResponseCode'] = "200";
$response_data['Result'] = "true";
$response_data['ResponseMsg'] = "true";
$response_data['payments'] = $categories;
echo json_encode($response_data);
