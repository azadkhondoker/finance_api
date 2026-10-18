<?php
include '../connection.php';
include '../auth_check.php';

$id = $_GET['id'];

// Soft delete: row thake kintu deleted_at set hoy, total theke bad jay
$res = $cc->common_update('savings', ['deleted_at' => date('Y-m-d H:i:s')], ['id' => $id]);

echo json_encode($res);
