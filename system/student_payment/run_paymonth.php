<?php
ob_start();
include '../../init.php';
extract($_GET);

$db = dbConn();
$Error = [];
$sql = "SELECT c.Id AS class_id, c.Grade_Level_id,se.student_id,c.class_fee,c.Teacher_id FROM `student_enroll` se 
INNER JOIN classes c ON se.class_id=c.Id";

$result = $db->query($sql);
while ($row = $result->fetch_assoc()) {
  $classId = $row['class_id'];
  $gradeId = $row['Grade_Level_id'];
  $studentId = $row['student_id'];
  $amount = $row['class_fee'];
  $teacherId = $row['Teacher_id'];




  $sql = "SELECT * FROM student_payment WHERE student_id ='$studentId' AND class_id='$classId' AND 
            pay_year='$current_year' AND pay_month='$current_month'";
  $result_1 = $db->query($sql);

  if ($result_1->num_rows > 0) {
  } else {
    $sql = "INSERT INTO `student_payment`( `student_id`, `grade_id`, `amount`, `status`, `pay_year`, `pay_month`, `class_id`) VALUES 
   ('$studentId','$gradeId','$amount','Pending','$current_year','$current_month','$classId')";
    $db->query($sql);
  }
}
