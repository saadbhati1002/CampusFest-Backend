<?php
require dirname(dirname(__FILE__)) . '/include/eventconfig.php';
header('Content-type: text/json');
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    echo json_encode(array("ResponseCode" => "405", "Result" => "false", "ResponseMsg" => "Method not allowed"));
    return;
}
$uid = isset($_SERVER['HTTP_UID']) ? $_SERVER['HTTP_UID'] : '';
$admin_type = '';
if ($uid == '') {
    echo json_encode(array("ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Unauthorized"));
    return;
}

if ($uid == '' or checkAdmin($uid) <= 0) {

    echo json_encode(array("ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Unauthorized"));
    return;
}

$admin_type = getAdminType($uid);

$response_data = [
    'ResponseCode' => "200",
    'Result' => "true",
    'ResponseMsg' => ''
];

$dashboard_data['total_categories'] = $event->query("select * from tbl_cat")->num_rows;
$dashboard_data['total_event'] = $event->query("select * from tbl_event")->num_rows;
$dashboard_data['total_pages'] = $event->query("select * from tbl_page")->num_rows;
$dashboard_data['total_faq_categories'] = $event->query("select * from faq_cat")->num_rows;
$dashboard_data['total_faqs'] = $event->query("select * from tbl_faq")->num_rows;
$dashboard_data['total_users'] = $event->query("select * from tbl_user")->num_rows;
$dashboard_data['total_gallery_images'] = $event->query("select * from tbl_gallery")->num_rows;
$dashboard_data['total_offers'] = $event->query("select * from tbl_coupon")->num_rows;
$dashboard_data['total_tickets'] = $event->query("select * from tbl_ticket where ticket_type='Completed'")->num_rows;
$dashboard_data['total_sales'] = number_format((float)$event->query("select sum(`total_amt`) as total_sales from tbl_ticket where ticket_type='Completed'")->fetch_assoc()['total_sales'], 2, '.', '') . $set['currency'];
$dashboard_data['total_admins'] = $event->query("select * from admin where admin_type='SUB_ADMIN'")->num_rows;

$response_data['dashboard_counts'] = $dashboard_data;
echo json_encode($response_data);
