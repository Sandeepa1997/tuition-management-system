<?php
ob_start();
include '../../init.php';



?>



<?php
//  Check login: allow only logged-in users
if (!isset($_SESSION['ID'])) { ?>
    <div class='container py-5'>
        <div class='alert alert-warning shadow-sm rounded'>
            <strong>You must be logged in to make an appointment.</strong><br>
            <a href='../login.php' class='btn btn-primary mt-3'>Login</a>
            <a href='../register/register.php' class='btn btn-secondary mt-3 ms-2'>Register</a>
        </div>
    </div>
    <?php
} else {




    $db = dbConn();
    $Userid = $_SESSION['ID'];

    $date = $_SESSION['APP_DATE'];
    $time = $_SESSION['APP_TIME'];
    $reason = $_SESSION['REASON'];
    $teacher_id = $_SESSION['TEACHER_ID'];

    $messages = null;

    if ($date && $time && $reason && $teacher_id) {

        // Get parent ID
        $sql = "SELECT Id FROM parents WHERE Userid = '$Userid'";
        $result = $db->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $parentid = $row['Id'];

            // Time range
            $start = new DateTime("$date $time");
            $end = clone $start;
            $end->modify('+15 minutes');
            $new_start = $start->format('H:i:s');
            $new_end = $end->format('H:i:s');

            // Check if slot is taken
            $sql = "SELECT * FROM appointments 
                WHERE appointment_date = '$date' 
                  AND teacher_id = '$teacher_id'
                  AND (TIME(appointment_time) < '$new_end' AND ADDTIME(appointment_time, '00:15:00') > '$new_start')";
            $result = $db->query($sql);

            if ($result->num_rows > 0) {
                $messages = "<div class='alert alert-danger'> This time slot is already booked for the selected teacher. Please try a different time.</div>";
            } else {
                // Book appointment
                $appointment_ref = "APT" . strtoupper(uniqid());

                $sql = "INSERT INTO appointments (appointment_ref, parent_id, teacher_id, appointment_date, appointment_time, reason)
                    VALUES ('$appointment_ref', '$parentid', '$teacher_id', '$date', '$time', '$reason')";
                $db->query($sql);

                $messages = "<div class='alert alert-success'>
                            Your appointment has been booked successfully!<br>
                            <strong>Date:</strong> $date<br>
                            <strong>Time:</strong> $time<br>
                            <strong>Teacher ID:</strong> $teacher_id<br>
                            <strong>Reference Number:</strong> $appointment_ref
                         </div>";
            }
        }
    ?>

        <title>Confirm Appointment</title>

        <body class="bg-light">
            <div class="container py-5">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <?= $messages ?>
                    </div>
                </div>
            </div>
        </body>

        </html>

<?php
    }
}
$content = ob_get_clean();
include '../layouts.php';
?>