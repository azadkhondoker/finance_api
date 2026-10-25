<?php
include '../connection.php';
include '../auth_check.php';

// ?type=land|business|gold diye filter kora jay
$where = [];
if (!empty($_GET['type']) && in_array($_GET['type'], ['land', 'business', 'gold'], true)) {
    $where['asset_type'] = $_GET['type'];
}

$result = $cc->common_select('assets', '*', $where, 'AND', 'invest_date DESC, id', 'DESC');

$active = 0;
$profit = 0;
foreach ($result['data'] as $row) {
    if ($row->status === 'active') {
        $active += (float) $row->amount;
    } else {
        $profit += (float) $row->sale_amount - (float) $row->amount;
    }
}
$result['total_active'] = $active;
$result['total_profit'] = $profit;

echo json_encode($result);
