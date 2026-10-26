<?php
include '../connection.php';
include '../auth_check.php';
include '../funds.php';
$id = (int) $_GET['id'];

$result = $cc->common_select('loans', '*', ['id' => $id]);

if ($result['status']) {
    // Edit page-e kon field lock thakbe seta bujhte
    $inst_count = (int) sum_query($cc, "SELECT COUNT(*) AS total FROM loan_installments WHERE loan_id = $id AND deleted_at IS NULL");
    $paid_inst = (int) sum_query($cc, "SELECT COUNT(*) AS total FROM loan_installments WHERE loan_id = $id AND deleted_at IS NULL AND status = 'paid'");
    $any_payment = (int) sum_query($cc, "SELECT COUNT(*) AS total FROM loan_payments WHERE loan_id = $id AND deleted_at IS NULL");
    $result['has_schedule'] = $inst_count > 0;
    $result['schedule_locked'] = $paid_inst > 0 || ($inst_count == 0 && $any_payment > 0);
}

echo json_encode($result);
