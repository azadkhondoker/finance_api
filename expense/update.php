<?php
date_default_timezone_set('Asia/Dhaka');
include '../connection.php';
include '../auth_check.php';
$data = json_decode(file_get_contents('php://input'), true);
$id = $_GET['id'];

if (!is_array($data) || empty(trim(isset($data['title']) ? $data['title'] : '')) || !isset($data['amount']) || !is_numeric($data['amount']) || (float) $data['amount'] <= 0) {
    echo json_encode(array("message" => "Title and a valid amount are required.", "status" => false, "data" => []));
    exit;
}

// common_update value escape kore na, tai ekhane nijei escape kore nicchi
$esc = function ($v) use ($cc) {
    return $cc->conn->real_escape_string($v);
};

// expense_at (original date ar time) ager moto-i thake, shudhu updated_at bodlay
$update = [
    'title' => $esc(trim($data['title'])),
    'amount' => (float) $data['amount'],
    'note' => $esc(isset($data['note']) ? $data['note'] : ''),
    'updated_at' => date('Y-m-d H:i:s')
];

$res = $cc->common_update('expenses', $update, ['id' => $id]);

echo json_encode($res);
