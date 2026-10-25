<?php
include '../connection.php';
include '../auth_check.php';
include '../funds.php';

// Total savings, total profit, total expense, net profit (profit - expense), ar invest-er hishab
$funds = get_funds($cc);

echo json_encode(array_merge($funds, array(
    "status" => true,
    "message" => "Records found"
)));
