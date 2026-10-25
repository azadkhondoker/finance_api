<?php
date_default_timezone_set('Asia/Dhaka');
include '../connection.php';
include '../auth_check.php';
include '../funds.php';

// Asset bikri korle: bikri-r daam taka cash-e ferot ashe, laabh/khoti hishabe jug hoy
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$data = json_decode(file_get_contents('php://input'), true);

if (!is_array($data) || !isset($data['sale_amount']) || !is_numeric($data['sale_amount']) || (float) $data['sale_amount'] < 0) {
    json_fail('A valid sale amount is required.');
}
$date = !empty($data['sale_date']) ? $data['sale_date'] : date('Y-m-d');
if (!valid_date($date)) {
    json_fail('Invalid date.');
}

$current = $cc->common_select('assets', '*', ['id' => $id]);
if (!$current['status'] || $current['data'][0]->status !== 'active') {
    json_fail('Only an active asset can be marked as sold.');
}

echo json_encode($cc->common_update('assets', [
    'status' => 'sold',
    'sale_amount' => (float) $data['sale_amount'],
    'sale_date' => $date,
    'updated_at' => date('Y-m-d H:i:s')
], ['id' => $id]));
