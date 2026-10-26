<?php
date_default_timezone_set('Asia/Dhaka');
include '../connection.php';
include '../auth_check.php';
include '../loan_schedule.php';

// Ekta loan + tar sob kisti (installment) + sob ferot (payment) + hishab
$id = isset($_GET['id']) ? $_GET['id'] : '';

$loan = $cc->common_select('loans', '*', ['id' => $id]);
if (!$loan['status'] || count($loan['data']) == 0) {
    echo json_encode(array("message" => "Loan not found.", "status" => false, "data" => []));
    exit;
}
$row = $loan['data'][0];
$today = date('Y-m-d');

$payments = $cc->common_select('loan_payments', '*', ['loan_id' => $row->id], 'AND', 'payment_date DESC, id', 'DESC');

$paid_principal = 0;
$paid_interest = 0;
foreach ($payments['data'] as $p) {
    $paid_principal += (float) $p->principal_amount;
    $paid_interest += (float) $p->interest_amount;
}

// Kisti gulo: status (paid / due / out_of_time), jorimana, jorimana shoho payable
$inst = $cc->common_select('loan_installments', '*', ['loan_id' => $row->id], 'AND', 'installment_no', 'ASC');
$penalty_rate = effective_penalty_rate($row);
$total_interest = 0;
$paid_count = 0;
$overdue_count = 0;
$overdue_penalty = 0;
foreach ($inst['data'] as $i) {
    $i->status_label = installment_status($i, $today);
    $i->installment_amount = round((float) $i->principal_part + (float) $i->interest_part, 2);
    if ($i->status === 'paid') {
        $i->penalty = (float) $i->penalty_paid;
        $i->days_late = 0;
        $paid_count++;
    } else {
        $i->penalty = installment_penalty($i, $penalty_rate, $today);
        $i->days_late = days_late($i->due_date, $today);
        if ($i->status_label === 'out_of_time') {
            $overdue_count++;
            $overdue_penalty += $i->penalty;
        }
    }
    $i->payable = round($i->installment_amount + $i->penalty, 2);
    $total_interest += (float) $i->interest_part;
}

// Schedule na thakle ager moto shomoyshima theke expected interest
$expected_interest = null;
$has_schedule = count($inst['data']) > 0;
if ($has_schedule) {
    $expected_interest = round($total_interest, 2);
} elseif (!empty($row->due_date)) {
    $days = (strtotime($row->due_date) - strtotime($row->loan_date)) / 86400;
    if ($days > 0) {
        $expected_interest = round((float) $row->principal * (float) $row->interest_rate / 100 * $days / 365, 2);
    }
}

echo json_encode(array(
    "status" => true,
    "message" => "Records found",
    "loan" => $row,
    "payments" => $payments['data'],
    "installments" => $inst['data'],
    "has_schedule" => $has_schedule,
    "installment_count" => count($inst['data']),
    "paid_count" => $paid_count,
    "overdue_count" => $overdue_count,
    "overdue_penalty" => round($overdue_penalty, 2),
    "penalty_rate" => $penalty_rate,
    "total_payable" => $has_schedule ? round((float) $row->principal + $total_interest, 2) : null,
    "paid_principal" => $paid_principal,
    "paid_interest" => $paid_interest,
    "outstanding" => (float) $row->principal - $paid_principal,
    "expected_interest" => $expected_interest
));
