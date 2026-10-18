<?php
include '../connection.php';
include '../auth_check.php';

// Ekta member-er info + tar sob joma (deposit) history
$member_id = isset($_GET['member_id']) ? $_GET['member_id'] : '';

$member = $cc->common_select('members', '*', ['id' => $member_id]);
if (!$member['status'] || count($member['data']) == 0) {
    echo json_encode(array("message" => "Member not found.", "status" => false, "data" => []));
    exit;
}

$history = $cc->common_select('savings', '*', ['member_id' => $member_id], 'AND', 'saving_date DESC, savings.id', 'DESC');

$total = 0;
foreach ($history['data'] as $row) {
    $total += (float) $row->amount;
}

echo json_encode(array(
    "status" => true,
    "message" => "Records found",
    "member" => $member['data'][0],
    "data" => $history['data'],
    "total_savings" => $total
));
