<?php
date_default_timezone_set('Asia/Dhaka');
include '../connection.php';
include '../auth_check.php';
include '../funds.php';
$data = json_decode(file_get_contents('php://input'), true);
$id = (int) $_GET['id'];

$type = is_array($data) && isset($data['asset_type']) ? $data['asset_type'] : '';
$title = is_array($data) && isset($data['title']) ? trim($data['title']) : '';
if (!in_array($type, ['land', 'business', 'gold'], true) || $title === '' || !isset($data['amount']) || !is_numeric($data['amount']) || (float) $data['amount'] <= 0) {
    json_fail('Type, title and a valid amount are required.');
}
$date = !empty($data['invest_date']) ? $data['invest_date'] : date('Y-m-d');
if (!valid_date($date)) {
    json_fail('Invalid date.');
}

$current = $cc->common_select('assets', '*', ['id' => $id]);
if (!$current['status']) {
    json_fail('Asset not found.');
}

// Active asset-er amount barale available cash-er moddhe thakte hobe
if ($current['data'][0]->status === 'active') {
    $funds = get_funds($cc, 0, $id);
    if ((float) $data['amount'] > $funds['available']) {
        json_fail('Not enough available savings. Available: ' . number_format($funds['available'], 2));
    }
}

$esc = function ($v) use ($cc) {
    return $cc->conn->real_escape_string($v);
};

echo json_encode($cc->common_update('assets', [
    'asset_type' => $type,
    'title' => $esc($title),
    'details' => $esc(isset($data['details']) ? $data['details'] : ''),
    'amount' => (float) $data['amount'],
    'invest_date' => $date,
    'note' => $esc(isset($data['note']) ? $data['note'] : ''),
    'updated_at' => date('Y-m-d H:i:s')
], ['id' => $id]));
