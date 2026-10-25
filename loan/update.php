<?php
date_default_timezone_set('Asia/Dhaka');
include '../connection.php';
include '../auth_check.php';
include '../funds.php';
$data = json_decode(file_get_contents('php://input'), true);
$id = (int) $_GET['id'];

$name = is_array($data) && isset($data['borrower_name']) ? trim($data['borrower_name']) : '';
if ($name === '' || !isset($data['principal']) || !is_numeric($data['principal']) || (float) $data['principal'] <= 0) {
    json_fail('Borrower name and a valid amount are required.');
}
$rate = isset($data['interest_rate']) && $data['interest_rate'] !== '' ? $data['interest_rate'] : 0;
if (!is_numeric($rate) || (float) $rate < 0) {
    json_fail('Interest rate must be 0 or more.');
}
$loan_date = !empty($data['loan_date']) ? $data['loan_date'] : date('Y-m-d');
if (!valid_date($loan_date) || (!empty($data['due_date']) && !valid_date($data['due_date']))) {
    json_fail('Invalid date.');
}

$principal = (float) $data['principal'];

// Ekhon porjonto je mul taka ferot esheche tar cheye kom principal kora jabe na
$paid = sum_query($cc, "SELECT COALESCE(SUM(principal_amount), 0) AS total FROM loan_payments WHERE loan_id = $id AND deleted_at IS NULL");
if ($principal < $paid) {
    json_fail('Principal cannot be less than the amount already repaid (' . number_format($paid, 2) . ').');
}

// Ei loan bad diye available cash-er beshi hole hobe na
$funds = get_funds($cc, $id);
if ($principal > $funds['available']) {
    json_fail('Not enough available savings. Available: ' . number_format($funds['available'], 2));
}

$esc = function ($v) use ($cc) {
    return $cc->conn->real_escape_string($v);
};

$res = $cc->common_update('loans', [
    'borrower_name' => $esc($name),
    'borrower_phone' => $esc(isset($data['borrower_phone']) ? $data['borrower_phone'] : ''),
    'principal' => $principal,
    'interest_rate' => (float) $rate,
    'loan_date' => $loan_date,
    'note' => $esc(isset($data['note']) ? $data['note'] : ''),
    'updated_at' => date('Y-m-d H:i:s')
], ['id' => $id]);

// Faka thakle due_date / member_id NULL kore dei
if (empty($data['due_date'])) {
    $cc->conn->query("UPDATE loans SET due_date = NULL WHERE id = $id");
} else {
    $cc->conn->query("UPDATE loans SET due_date = '" . $esc($data['due_date']) . "' WHERE id = $id");
}
if (!empty($data['member_id']) && ctype_digit((string) $data['member_id'])) {
    $cc->conn->query("UPDATE loans SET member_id = " . (int) $data['member_id'] . " WHERE id = $id");
} else {
    $cc->conn->query("UPDATE loans SET member_id = NULL WHERE id = $id");
}

echo json_encode($res);
