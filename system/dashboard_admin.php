<?php
// Include your database connection file, or define the dbConn() function here.
// For example:


// Database connection
$db = dbConn();

// Query for total user registrations
$sql_users = "SELECT COUNT(*) AS total_users FROM users";
$result_users = $db->query($sql_users);
$row_users = $result_users->fetch_assoc();
$total_users = $row_users['total_users'];

// Query for total student enrollments
$sql_enrollments = "SELECT COUNT(*) AS total_enrollments FROM student_enroll";
$result_enrollments = $db->query($sql_enrollments);
$row_enrollments = $result_enrollments->fetch_assoc();
$total_enrollments = $row_enrollments['total_enrollments'];

// Query for total feedbacks
$sql_feedbacks = "SELECT COUNT(*) AS total_feedbacks FROM feedback";
$result_feedbacks = $db->query($sql_feedbacks);
$row_feedbacks = $result_feedbacks->fetch_assoc();
$total_feedbacks = $row_feedbacks['total_feedbacks'];

// Query for total attendance records
$sql_attendance = "SELECT COUNT(*) AS total_attendance FROM class_attendance";
$result_attendance = $db->query($sql_attendance);
$row_attendance = $result_attendance->fetch_assoc();
$total_attendance = $row_attendance['total_attendance'];

$db->close();
?>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?php echo $total_attendance; ?></h3>
                <p>Total Attendance</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?php echo $total_enrollments; ?></h3>
                <p>Student Enrollments</p>
            </div>
            <div class="icon">
                <i class="ion ion-person-add"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?php echo $total_users; ?></h3>
                <p>User Registrations</p>
            </div>
            <div class="icon">
                <i class="ion ion-person-add"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?php echo $total_feedbacks; ?></h3>
                <p>Total Feedbacks</p>
            </div>
            <div class="icon">
                <i class="ion ion-pie-graph"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    </div>
    <?php





