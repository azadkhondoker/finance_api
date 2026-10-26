<?php
date_default_timezone_set('Asia/Dhaka');
include '../connection.php';
include '../auth_check.php';
include '../funds.php';
include '../loan_schedule.php';
$data = json_decode(file_get_contents('php://input'), true);
$id = (int) $_GET['id'];

if (!is_array($data)) {
    json_fail('Invalid request.');
}
$name = isset($data['borrower_name']) ? trim($data['borrower_name']) : '';
if ($name === '') {
    json_fail('Borrower name is required.');
}

$esc = function ($v) use ($cc) {
    return $cc->conn->real_escape_string($v);
};

$current = $cc->common_select('loans', '*', ['id' => $id]);
if (!$current['status']) {
    json_fail('Loan not found.');
}

$inst_count = (int) sum_query($cc, "SELECT COUNT(*) AS total FROM loan_installments WHERE loan_id = $id AND deleted_at IS NULL");
$paid_inst = (int) sum_query($cc, "SELECT COUNT(*) AS total FROM loan_installments WHERE loan_id = $id AND deleted_at IS NULL AND status = 'paid'");
$any_payment = (int) sum_query($cc, "SELECT COUNT(*) AS total FROM loan_payments WHERE loan_id = $id AND deleted_at IS NULL");
$has_schedule = $inst_count > 0;
$locked = $paid_inst > 0 || (!$has_schedule && $any_payment > 0);

$info = [
    'borrower_name' => $esc($name),
    'borrower_phone' => $esc(isset($data['borrower_phone']) ? $data['borrower_phone'] : ''),
    'note' => $esc(isset($data['note']) ? $data['note'] : ''),
    'updated_at' => date('Y-m-d H:i:s')
];

// Member link (optional)
$member_sql = (!empty($data['member_id']) && ctype_digit((string) $data['member_id'])) ? (int) $data['member_id'] : 'NULL';

// 1) Schedule-wala loan-e kisti ekbar paid hole shorto (taka, interest, mash, dhoron) bodlano jabe na
if ($has_schedule && $locked) {
    $res = $cc->common_update('loans', $info, ['id' => $id]);
    $cc->conn->query("UPDATE loans SET member_id = $member_sql WHERE id = $id");
    echo json_encode($res);
    exit;
}

// Baki shob khetre taka, interest, tarikh validate kori
if (!isset($data['principal']) || !is_numeric($data['principal']) || (float) $data['principal'] <= 0) {
    json_fail('A valid amount is required.');
}
$rate = isset($data['interest_rate']) && $data['interest_rate'] !== '' ? $data['interest_rate'] : 0;
if (!is_numeric($rate) || (float) $rate < 0) {
    json_fail('Interest rate must be 0 or more.');
}
$loan_date = !empty($data['loan_date']) ? $data['loan_date'] : date('Y-m-d');
if (!valid_date($loan_date)) {
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

$info['principal'] = $principal;
$info['interest_rate'] = (float) $rate;
$info['loan_date'] = $loan_date;

// 2) Purono loan (kisti nei) je-te kono ferot hoyeche: ager moto edit, schedule jug hobe na
if ($locked && !$has_schedule) {
    $res = $cc->common_update('loans', $info, ['id' => $id]);
    $cc->conn->query("UPDATE loans SET member_id = $member_sql WHERE id = $id");
    echo json_encode($res);
    exit;
}

// 3) Kono kisti paid hoy nai: schedule notun kore toiri hoy
$months = isset($data['duration_months']) ? $data['duration_months'] : '';
$type = isset($data['repayment_type']) ? $data['repayment_type'] : 'monthly';
$penalty_rate = isset($data['penalty_rate']) && $data['penalty_rate'] !== '' && $data['penalty_rate'] !== null ? $data['penalty_rate'] : null;
if (!ctype_digit((string) $months) || (int) $months < 1 || (int) $months > 600) {
    json_fail('Loan duration (months) must be a whole number, 1 or more.');
}
if (!valid_repayment_type($type)) {
    json_fail('Choose weekly, half-monthly or monthly installments.');
}
if ($penalty_rate !== null && (!is_numeric($penalty_rate) || (float) $penalty_rate < 0)) {
    json_fail('Penalty rate must be 0 or more.');
}

$schedule = build_schedule($principal, (float) $rate, (int) $months, $type, $loan_date);
$info['due_date'] = $schedule['due_date'];
$info['duration_months'] = (int) $months;
$info['repayment_type'] = $type;

$cc->conn->begin_transaction();
$res = $cc->common_update('loans', $info, ['id' => $id]);
if (!$res['status']) {
    $cc->conn->rollback();
    json_fail($res['message']);
}
$cc->conn->query("UPDATE loans SET member_id = $member_sql, penalty_rate = " . ($penalty_rate === null ? 'NULL' : (float) $penalty_rate) . " WHERE id = $id");
$cc->conn->query("DELETE FROM loan_installments WHERE loan_id = $id");
foreach ($schedule['installments'] as $row) {
    $ins = $cc->common_insert('loan_installments', [
        'loan_id' => $id,
        'installment_no' => $row['installment_no'],
        'due_date' => $row['due_date'],
        'principal_part' => $row['principal_part'],
        'interest_part' => $row['interest_part'],
        'status' => 'unpaid',
        'created_at' => date('Y-m-d H:i:s')
    ]);
    if (!$ins['status']) {
        $cc->conn->rollback();
        json_fail($ins['message']);
    }
}
$cc->conn->commit();

echo json_encode($res);
