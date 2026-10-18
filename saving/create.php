<?php
include '../connection.php';
include '../auth_check.php';

$res = [];
$member_id = isset($_POST['member_id']) ? $_POST['member_id'] : '';
$amount = isset($_POST['amount']) ? $_POST['amount'] : '';
$saving_date = !empty($_POST['saving_date']) ? $_POST['saving_date'] : date('Y-m-d');

if (!ctype_digit((string) $member_id) || !is_numeric($amount) || (float) $amount <= 0) {
    echo json_encode(array("message" => "Valid member and amount are required.", "status" => false, "data" => []));
    exit;
}

$member = $cc->common_select('members', '*', ['id' => $member_id]);
if (!$member['status']) {
    echo json_encode(array("message" => "Member not found.", "status" => false, "data" => []));
    exit;
}

$res = $cc->common_insert('savings', [
    'member_id' => $member_id,
    'amount' => (float) $amount,
    'saving_date' => $saving_date,
    'note' => isset($_POST['note']) ? $_POST['note'] : '',
    'created_at' => date('Y-m-d H:i:s')
]);
echo json_encode($res);
