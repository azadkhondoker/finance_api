<?php
date_default_timezone_set('Asia/Dhaka');
include '../connection.php';
include '../auth_check.php';
include '../funds.php';

$type = isset($_POST['asset_type']) ? $_POST['asset_type'] : '';
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$amount = isset($_POST['amount']) ? $_POST['amount'] : '';
$date = !empty($_POST['invest_date']) ? $_POST['invest_date'] : date('Y-m-d');

if (!in_array($type, ['land', 'business', 'gold'], true) || $title === '' || !is_numeric($amount) || (float) $amount <= 0) {
    json_fail('Type, title and a valid amount are required.');
}
if (!valid_date($date)) {
    json_fail('Invalid date.');
}

// Available cash-er beshi invest kora jabe na
$funds = get_funds($cc);
if ((float) $amount > $funds['available']) {
    json_fail('Not enough available savings. Available: ' . number_format($funds['available'], 2));
}

echo json_encode($cc->common_insert('assets', [
    'asset_type' => $type,
    'title' => $title,
    'details' => isset($_POST['details']) ? $_POST['details'] : '',
    'amount' => (float) $amount,
    'invest_date' => $date,
    'status' => 'active',
    'note' => isset($_POST['note']) ? $_POST['note'] : '',
    'created_at' => date('Y-m-d H:i:s')
]));
