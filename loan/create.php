<?php
date_default_timezone_set('Asia/Dhaka');
include '../connection.php';
include '../auth_check.php';
include '../funds.php';

$name = isset($_POST['borrower_name']) ? trim($_POST['borrower_name']) : '';
$principal = isset($_POST['principal']) ? $_POST['principal'] : '';
$rate = isset($_POST['interest_rate']) && $_POST['interest_rate'] !== '' ? $_POST['interest_rate'] : 0;
$loan_date = !empty($_POST['loan_date']) ? $_POST['loan_date'] : date('Y-m-d');

if ($name === '' || !is_numeric($principal) || (float) $principal <= 0) {
    json_fail('Borrower name and a valid amount are required.');
}
if (!is_numeric($rate) || (float) $rate < 0) {
    json_fail('Interest rate must be 0 or more.');
}
if (!valid_date($loan_date) || (!empty($_POST['due_date']) && !valid_date($_POST['due_date']))) {
    json_fail('Invalid date.');
}

// Available cash-er beshi loan deya jabe na
$funds = get_funds($cc);
if ((float) $principal > $funds['available']) {
    json_fail('Not enough available savings. Available: ' . number_format($funds['available'], 2));
}

$data = [
    'borrower_name' => $name,
    'borrower_phone' => isset($_POST['borrower_phone']) ? $_POST['borrower_phone'] : '',
    'principal' => (float) $principal,
    'interest_rate' => (float) $rate,
    'loan_date' => $loan_date,
    'note' => isset($_POST['note']) ? $_POST['note'] : '',
    'created_at' => date('Y-m-d H:i:s')
];
if (!empty($_POST['member_id']) && ctype_digit((string) $_POST['member_id'])) {
    $data['member_id'] = $_POST['member_id'];
}
if (!empty($_POST['due_date'])) {
    $data['due_date'] = $_POST['due_date'];
}

echo json_encode($cc->common_insert('loans', $data));
