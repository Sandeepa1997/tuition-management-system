<?php
ob_start();
include '../../init.php';
?>"



<?php

$messages=null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    extract($_POST);

    //create start time using php Datetime class
    $start = new DateTime("$date $time");
    $end = clone $start;
    $end->modify('+15 minutes');

    $new_start = $start->format('Y-m-d H:i:s');
    $new_end = $end->format('Y-m-d H:i:s');

    $db = dbConn();
 $sql = "SELECT * FROM appointments WHERE appointment_date='$date' AND 
    (TIME(CONCAT(appointment_date,' ',appointment_time)) < TIME('$new_end') AND ADDTIME(appointment_time,'00:15:00')> TIME('$new_start'))";
    $result = $db->query($sql);
   

    if ($result->num_rows>0) {

        $messages="This time slot has already booked!!!, try a different timeslot!!!";

    } else {

        $appointment_ref = "APT" . strtoupper(uniqid());

     $sql = "INSERT INTO appointments(appointment_ref,parent_name,appointment_date,appointment_time) VALUES
     ('$appointment_ref','$parent_name','$date','$time')";
     $db->query($sql);

     $messages="Your appointment has been booked succesfully!!! <br> 
      Date:<strong>$date</strong> <br> 
      Time:$time</strong><br>
      Reference Number:<strong>$appointment_ref</strong>";

    }
}

?>







<?php
$content = ob_get_clean();
include '../layouts.php';
?>