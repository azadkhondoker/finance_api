<?php
// Savings theke invest-er hishab ekhane ek jaygay rakha ache.
//
// Total Savings: member-der joma, expense-e kom hoy na.
// Total Profit  = Interest pawa + Bikri kora asset-er laabh/khoti
// Net Profit    = Total Profit - Total Expense  (expense profit theke bad jay)
//
// Available cash = Total Savings + Net Profit
//                  - Loan-e ekhono baki mul taka
//                  - Ekhono active asset-e atkano taka

function json_fail($message)
{
    echo json_encode(array("message" => $message, "status" => false, "data" => []));
    exit;
}

function valid_date($d)
{
    return is_string($d) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) && strtotime($d) !== false;
}

function sum_query($cc, $sql)
{
    $r = $cc->common_query($sql);
    return $r['status'] ? (float) $r['data'][0]->total : 0.0;
}

function get_funds($cc, $exclude_loan_id = 0, $exclude_asset_id = 0)
{
    $exclude_loan_id = (int) $exclude_loan_id;
    $exclude_asset_id = (int) $exclude_asset_id;

    $savings = sum_query($cc, "SELECT COALESCE(SUM(s.amount), 0) AS total FROM savings s
        INNER JOIN members m ON m.id = s.member_id AND m.deleted_at IS NULL
        WHERE s.deleted_at IS NULL");
    $expense = sum_query($cc, "SELECT COALESCE(SUM(amount), 0) AS total FROM expenses WHERE deleted_at IS NULL");

    $loan_given = sum_query($cc, "SELECT COALESCE(SUM(principal), 0) AS total FROM loans
        WHERE deleted_at IS NULL AND id <> $exclude_loan_id");
    $loan_repaid = sum_query($cc, "SELECT COALESCE(SUM(p.principal_amount), 0) AS total FROM loan_payments p
        INNER JOIN loans l ON l.id = p.loan_id AND l.deleted_at IS NULL
        WHERE p.deleted_at IS NULL AND l.id <> $exclude_loan_id");
    $interest = sum_query($cc, "SELECT COALESCE(SUM(p.interest_amount), 0) AS total FROM loan_payments p
        INNER JOIN loans l ON l.id = p.loan_id AND l.deleted_at IS NULL
        WHERE p.deleted_at IS NULL AND l.id <> $exclude_loan_id");

    $asset_active = sum_query($cc, "SELECT COALESCE(SUM(amount), 0) AS total FROM assets
        WHERE deleted_at IS NULL AND status = 'active' AND id <> $exclude_asset_id");
    $asset_profit = sum_query($cc, "SELECT COALESCE(SUM(sale_amount - amount), 0) AS total FROM assets
        WHERE deleted_at IS NULL AND status = 'sold' AND id <> $exclude_asset_id");

    $by_type = array('land' => 0, 'business' => 0, 'gold' => 0);
    $types = $cc->common_query("SELECT asset_type, COALESCE(SUM(amount), 0) AS total FROM assets
        WHERE deleted_at IS NULL AND status = 'active' AND id <> $exclude_asset_id GROUP BY asset_type");
    foreach ($types['data'] as $row) {
        $by_type[$row->asset_type] = (float) $row->total;
    }

    $loan_outstanding = $loan_given - $loan_repaid;
    $invested = $loan_outstanding + $asset_active;
    $total_profit = $interest + $asset_profit;
    $net_profit = $total_profit - $expense;

    return array(
        'total_savings' => $savings,
        'total_expense' => $expense,
        'total_profit' => $total_profit,
        'net_profit' => $net_profit,
        'loan_outstanding' => $loan_outstanding,
        'asset_active' => $asset_active,
        'asset_land' => $by_type['land'],
        'asset_business' => $by_type['business'],
        'asset_gold' => $by_type['gold'],
        'invested' => $invested,
        'interest_received' => $interest,
        'asset_profit' => $asset_profit,
        'available' => round($savings + $net_profit - $invested, 2)
    );
}
