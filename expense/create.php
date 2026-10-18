<?php
date_default_timezone_set('Asia/Dhaka');
include '../connection.php';
include '../auth_check.php';

$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$amount = isset($_POST['amount']) ? $_POST['amount'] : '';

if ($title === '' || !is_numeric($amount) || (float) $amount <= 0) {
    echo json_encode(array("message" => "Title and a valid amount are required.", "status" => false, "data" => []));
    exit;
}

// Date ar time server theke automatic generate hoy
$now = date('Y-m-d H:i:s');

$res = $cc->common_insert('expenses', [
    'title' => $title,
    'amount' => (float) $amount,
    'note' => isset($_POST['note']) ? $_POST['note'] : '',
    'expense_at' => $now,
    'created_at' => $now
]);
echo json_encode($res);
