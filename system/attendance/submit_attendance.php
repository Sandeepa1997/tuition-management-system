<?php
ob_start();
include '../../init.php';

//confirm whether login to the system
if (!isset($_SESSION['ID'])) {
    header("Location:../login.php");
}
?>

<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = dbConn();
    $class_id = $_POST['class_id'];
    $date = $_POST['date'];
    $attendance = $_POST['attendance'];

    foreach ($attendance as $student_id => $status) {
        $sql = "INSERT INTO class_attendance (student_id, class_id, date, status)
                VALUES ('$student_id', '$class_id', '$date', '$status')";
        $db->query($sql);
        
    }

   
}
?>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>