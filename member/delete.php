<?php
include '../connection.php';
include '../auth_check.php';

$id=$_GET['id'];
$get_res=$cc->common_select('members','*',['id'=>$id]);
if($get_res['status'] && count($get_res['data'])>0){
    $member=$get_res['data'][0];
    if(file_exists('../'.$member->image)){
        unlink('../'.$member->image);
    }
}
$res=$cc->common_delete('members',['id'=>$id]);

echo json_encode($res);