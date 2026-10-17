<?php
include '../connection.php';
include '../auth_check.php';

$uploadDir = '../';
$image="";
if (!empty($_FILES['image'])) {
    $name = rand().basename($_FILES['image']['name']);
    $targetPath = 'member/'.$name;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $targetPath)) {
        $_POST['image']=$targetPath;
    }
} 
$res=[];
if($_POST['name']){
    $res=$cc->common_insert('members', $_POST);
}
echo json_encode($res);

