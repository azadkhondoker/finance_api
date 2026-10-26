<?php
date_default_timezone_set('Asia/Dhaka');
include '../connection.php';
include '../auth_check.php';
include '../funds.php';

// Bhul kore paid kora kisti-ke abar Due kora
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$inst = $cc->common_select('loan_installments', '*', ['id' => $id]);
if (!$inst['status'] || $inst['data'][0]->status !== 'paid') {
    json_fail('Only a paid installment can be undone.');
}
$inst = $inst['data'][0];

$cc->conn->begin_transaction();
if (!empty($inst->payment_id)) {
    $cc->common_update('loan_payments', ['deleted_at' => date('Y-m-d H:i:s')], ['id' => (int) $inst->payment_id]);
}
$ok = $cc->conn->query("UPDATE loan_installments SET status = 'unpaid', paid_date = NULL, penalty_paid = 0,
    payment_id = NULL, updated_at = '" . date('Y-m-d H:i:s') . "' WHERE id = $id");
if (!$ok) {
    $cc->conn->rollback();
    json_fail('Could not undo the payment.');
}
$cc->conn->commit();

echo json_encode(array("status" => true, "data" => 1, "message" => "Installment payment undone"));
