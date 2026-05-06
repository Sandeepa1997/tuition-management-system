<?php
ob_start();
include '../../init.php';




  extract($_POST);

  if ($_SERVER['REQUEST_METHOD'] == 'POST' && @$action == 'check_date') {
    $db = dbConn();

    $date = trim($date);
    $reason = trim($reason);
    $teacher_id = (int)$teacher_id;

    // Get next available time
    
    $sql = "SELECT ADDTIME(TIME(appointment_time), '00:15:00') AS nexttime 
            FROM appointments 
            WHERE appointment_date = '$date' 
              AND teacher_id = '$teacher_id' 
            ORDER BY appointment_time DESC 
            LIMIT 1";
    $result = $db->query($sql);
    $row = $result->fetch_assoc();

    $nextapptime = $result->num_rows > 0 ? $row['nexttime'] : '09:30:00';

    // Get next appointment number
    $sql = "SELECT COUNT(*) + 1 AS next_app_no 
            FROM appointments 
            WHERE appointment_date = '$date' 
              AND teacher_id = '$teacher_id'";
    $result = $db->query($sql);
    $row = $result->fetch_assoc();
    $next_app_no = $row['next_app_no'];

    // Store in session
    $_SESSION['APP_DATE'] = $date;
    $_SESSION['APP_TIME'] = $nextapptime;
    $_SESSION['APP_NO'] = $next_app_no;
    $_SESSION['REASON'] = $reason;
    $_SESSION['TEACHER_ID'] = $teacher_id;

    // Preview output
    echo "<div class='container py-4'>";
    echo "<div class='alert alert-info shadow-sm rounded'>";
    echo "<h5 class='mb-2'>📅 Appointment Preview</h5>";
    echo "<p><strong>Date:</strong> $date</p>";
    echo "<p><strong>Next Available Time:</strong> $nextapptime</p>";
    echo "<p><strong>Appointment Number:</strong> $next_app_no</p>";
    echo "<p><strong>Reason:</strong> $reason</p>";

    // Get teacher name
    $sql = "SELECT FirstName, LastName FROM teachers WHERE Id = '$teacher_id'";
    $teacher = $db->query($sql)->fetch_assoc();
    echo "<p><strong>Teacher:</strong> {$teacher['FirstName']} {$teacher['LastName']}</p>";

    echo '<a href="confirm.php" class="btn btn-primary mt-3">Confirm My Appointment</a>';
    echo "</div></div>";
  }






$content = ob_get_clean();
include '../layouts.php';
?>