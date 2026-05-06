<?php
ob_start();
include '../../init.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);

    if (!empty($enroll_id)) {
        $db = dbConn();
        $sql = "DELETE FROM student_enroll WHERE Id = '$enroll_id'";
        $db->query($sql);
    }
}

header("Location:view.php");
die();
?>
