<?php
include '../../init.php';
$db = dbConn();

extract($_POST);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {



    $sql = "UPDATE student_payment SET status = '$status' WHERE Id = '$pay_id'";
   $db->query($sql);
   header('Location:view.php');
}
