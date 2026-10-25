<?php
date_default_timezone_set('Asia/Dhaka');
include '../connection.php';
include '../auth_check.php';

$id = $_GET['id'];

// Soft delete
$res = $cc->common_update('assets', ['deleted_at' => date('Y-m-d H:i:s')], ['id' => $id]);

echo json_encode($res);
