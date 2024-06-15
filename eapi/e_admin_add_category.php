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

$uid = isset($_GET['uid']) ? $_GET['uid'] : '';
$data = json_decode(file_get_contents('php://input'), true);


if ($uid == '' or checkAdmin($uid) <= 0) {
    echo json_encode(array("ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Unauthorized"));
    return;
}

$required_fields = ['title', 'status', 'img', 'cover_img'];

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
$table_name = "tbl_cat";
$fields = [
    'title', 'status', 'img', 'cover_img'
];

$category_data = [
    'title' => $data['title'],
    'status' => $data['status']
];

$img = str_replace(['data:image/png;base64,', 'data:image/jpg;base64,', 'data:image/jpeg;base64,'], '', $data['img']);
$img = str_replace(' ', '+', $data['img']);
$img_content = base64_decode($img);
$path = 'images/category/' . uniqid() . '.png';
$fname = dirname(dirname(__FILE__)) . '/' . $path;
file_put_contents($fname, $img_content);
$category_data['img'] = $path;



$cover_img = str_replace(['data:image/png;base64,', 'data:image/jpg;base64,', 'data:image/jpeg;base64,'], '', $data['cover_img']);
$cover_img = str_replace(' ', '+', $data['cover_img']);
$cover_img_content = base64_decode($img);
$path = 'images/category/' . uniqid() . '.png';
$fname = dirname(dirname(__FILE__)) . '/' . $path;
file_put_contents($fname, $cover_img_content);
$category_data['cover_img'] = $path;

try {

    $eventmedia = new Eventmania();
    $result = $eventmedia->eventinsertdata_Api($fields, $category_data, $table_name);
    if ($result) {
        echo json_encode(array("ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "Category has been added successfully."));
    }
} catch (Exception $e) {
    echo json_encode(array("ResponseCode" => "400", "Result" => "false", "ResponseMsg" => $e->getMessage()));
    return;
}



// $per_page = 10;
// $page = isset($_GET['page']) ? $_GET['page'] : 0;
// $search = isset($_GET['search']) ? $_GET['search'] : '';
// $response_data = [];
// $sql = "SELECT * FROM tbl_cat";

// if ($search != '') {
//     $sql .= " WHERE title LIKE '%$search%'";
// }

// if ($page > 0) {
//     $total_record = $event->query($sql)->num_rows;
//     $response_data['pagination'] = [
//         'total' => $total_record,
//         'current_page' => $page
//     ];
//     $offset = ($page - 1) * $per_page;
//     $sql .= " LIMIT $per_page OFFSET $offset";
// }

// $categories = [];
// $result = $event->query($sql);
// while ($row = $result->fetch_assoc()) {
//     $category = [
//         'id' => $row['id'],
//         'title' => $row['title'],
//         'img' => $row['img'],
//         'cover_img' => $row['cover_img'],
//         'status' => $row['status'] == 1 ? "Publish" : "Unpublish"
//     ];
//     array_push($categories, $category);
// }

// $response_data['categories'] = $categories;
// echo json_encode($response_data);
