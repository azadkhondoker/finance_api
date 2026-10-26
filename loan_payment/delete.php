<?php
date_default_timezone_set('Asia/Dhaka');
include '../connection.php';
include '../auth_check.php';

$id = $_GET['id'];

// Soft delete
$res = $cc->common_update('loan_payments', ['deleted_at' => date('Y-m-d H:i:s')], ['id' => $id]);

// Ei ferot kono kisti-r hole oi kisti abar Due hoye jay
$cc->conn->query("UPDATE loan_installments SET status = 'unpaid', paid_date = NULL, penalty_paid = 0, payment_id = NULL
    WHERE payment_id = " . (int) $id);

echo json_encode($res);
