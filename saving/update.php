<?php
include '../connection.php';
include '../auth_check.php';
$data = json_decode(file_get_contents('php://input'), true);
$id = $_GET['id'];

if (!is_array($data) || !isset($data['amount']) || !is_numeric($data['amount']) || (float) $data['amount'] <= 0) {
    echo json_encode(array("message" => "Valid amount is required.", "status" => false, "data" => []));
    exit;
}

// common_update value escape kore na, tai ekhane nijei escape kore nicchi
$esc = function ($v) use ($cc) {
    return $cc->conn->real_escape_string($v);
};

$update = [
    'amount' => (float) $data['amount'],
    'saving_date' => $esc(!empty($data['saving_date']) ? $data['saving_date'] : date('Y-m-d')),
    'note' => $esc(isset($data['note']) ? $data['note'] : ''),
    'updated_at' => date('Y-m-d H:i:s')
];

$res = $cc->common_update('savings', $update, ['id' => $id]);

echo json_encode($res);
