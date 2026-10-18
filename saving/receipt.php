<?php
include '../connection.php';
include '../auth_check.php';

// Ekta joma-r (deposit) pura receipt information: saving + member + total savings
$id = isset($_GET['id']) ? $_GET['id'] : '';

$saving = $cc->common_select('savings', '*', ['id' => $id]);
if (!$saving['status'] || count($saving['data']) == 0) {
    echo json_encode(array("message" => "Saving not found.", "status" => false, "data" => []));
    exit;
}
$row = $saving['data'][0];

$member = $cc->common_select('members', '*', ['id' => $row->member_id]);
if (!$member['status'] || count($member['data']) == 0) {
    echo json_encode(array("message" => "Member not found.", "status" => false, "data" => []));
    exit;
}

$member_id = (int) $row->member_id;
$saving_id = (int) $row->id;
$date = $cc->conn->real_escape_string($row->saving_date);

// Ei joma-r por member-er total savings (ager joma + ei joma)
$after = $cc->common_query("SELECT COALESCE(SUM(amount), 0) AS total FROM savings
    WHERE member_id = $member_id AND deleted_at IS NULL
    AND (saving_date < '$date' OR (saving_date = '$date' AND id <= $saving_id))");

// Ekhon member-er mot total savings
$current = $cc->common_query("SELECT COALESCE(SUM(amount), 0) AS total FROM savings
    WHERE member_id = $member_id AND deleted_at IS NULL");

echo json_encode(array(
    "status" => true,
    "message" => "Records found",
    "receipt_no" => 'RCP-' . str_pad($saving_id, 6, '0', STR_PAD_LEFT),
    "saving" => $row,
    "member" => $member['data'][0],
    "balance_after" => (float) $after['data'][0]->total,
    "total_savings" => (float) $current['data'][0]->total
));
