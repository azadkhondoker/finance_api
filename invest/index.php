<?php
include '../connection.php';
include '../auth_check.php';
include '../funds.php';

// Investment landing page-er summary
$funds = get_funds($cc);
$funds['status'] = true;
$funds['message'] = 'Records found';

echo json_encode($funds);
