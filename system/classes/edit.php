<?php
ob_start();
include '../../init.php';
?>

<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<?php

$db = dbConn();

extract($_POST);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && @$action == 'edit') {


 

    $sql = "SELECT * FROM classes WHERE Id='$Id'";
    $db = dbConn();
    $result = $db->query($sql);

    $row = $result->fetch_assoc();
    $start_time = $row['start_time'];
    $end_time = $row['end_time'];
    $classdate = $row['Class_Date'];
    $MaxStudents = $row['Maximum_Students'];
    $classfee = $row['class_fee'];
    $hallno = $row['hall_no'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $action == 'update') {



    $MaxStudents = dataclean($MaxStudents);
    $classfee = dataclean($classfee);


    $messages = array();

    // Empty Validation
    if (empty($start_time)) {
        $messages['start_time'] = "Please select a start time...";
    }
    if (empty($end_time)) {
        $messages['end_time'] = "Please select a end time...";
    }

    if (empty($classdate)) {
        $messages['classdate'] = "Please select the class date!...";
    }
    if (empty($MaxStudents)) {
        $messages['MaxStudents'] = "Please enter the maximum number of students!...";
    }
    if (empty($classfee)) {
        $messages['classfee'] = "Please enter the class fee!...";
    }

    // Time conflict check
    if (!empty($start_time) && !empty($end_time) && !empty($classdate) && !empty($hallno)) {
        $conflictSql = "SELECT * FROM classes 
                        WHERE Class_Date = '$classdate' 
                        AND (
                            (start_time <= '$start_time' AND end_time > '$start_time') OR
                            (start_time < '$end_time' AND end_time >= '$end_time') OR
                            ('$start_time' <= start_time AND '$end_time' >= end_time)
                        )
                        AND (
                            hall_no = '$hallno'
                        )";

        $resultConflict = $db->query($conflictSql);
        if ($resultConflict->num_rows > 0) {
            $messages['start_time'] = "Time conflict: Another class overlaps with this time.";
        }
    }

    //Insert into database//

    if (empty($messages)) {
        $db = dbConn();
        $sql = "UPDATE classes SET   
          Class_Date='$classdate',start_time='$start_time',end_time='$end_time',Maximum_Students='$MaxStudents',class_fee='$classfee',hall_no='$hallno'
           WHERE Id = '$Id'";

        $db->query($sql);
        echo '<script>
          Swal.fire({
position: "center",
icon: "success",
title: "Class has been updated",
showConfirmButton: false,
timer: 3500
}).then(function(){window.location="view.php"});
</script>';
    }
}
?>


<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title"> ScienceMore</h3>
    </div>

    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post" enctype="multipart/form-data">

        <div class="card-body">

            <!-- Start Time -->
            <div class="form-group">
                <label for="start_time">Start Time</label>
                <input type="time" name="start_time" id="start_time" class="form-control" value="<?= @$start_time ?>">
                <span class="text-danger"><?= @$messages['start_time'] ?></span>
            </div>

            <!-- End Time -->
            <div class="form-group">
                <label for="end_time">End Time</label>
                <input type="time" name="end_time" id="end_time" class="form-control"  value="<?= @$end_time ?>" >
                <span class="text-danger"><?= @$messages['end_time'] ?></span>
            </div>


            <!-- Class Date -->
            <div class="form-group mt-3">
                <label for="">Class Date</label>
                <select class="form-control" id="classdate" name="classdate">
                    <option value="">--</option>
                    <option value="Monday" <?= @$classdate == 'Monday' ? 'selected' : '' ?>>Monday</option>
                    <option value="Tuesday" <?= @$classdate == 'Tuesday' ? 'selected' : '' ?>>Tuesday</option>
                    <option value="Wednesday" <?= @$classdate == 'Wednesday' ? 'selected' : '' ?>>Wednesday</option>
                    <option value="Thursday" <?= @$classdate == 'Thursday' ? 'selected' : '' ?>>Thursday</option>
                    <option value="Friday" <?= @$classdate == 'Friday' ? 'selected' : '' ?>>Friday</option>
                    <option value="Saturday" <?= @$classdate == 'Saturday' ? 'selected' : '' ?>>Saturday</option>
                    <option value="Sunday" <?= @$classdate == 'Sunday' ? 'selected' : '' ?>>Sunday</option>
                </select>
            </div>


            <!-- Max Students -->
            <div class="form-group">
                <label for="MaxStudents">Maximum Students</label>
                <input type="number" class="form-control" name="MaxStudents" id="MaxStudents" min="1" value="<?= @$MaxStudents ?>">
                <span class="text-danger"><?= @$messages['MaxStudents'] ?></span>

            </div>

            <div class="form-group">
                <label for="classfee">Class Fee</label>
                <input type="text" class="form-control" name="classfee" id="classfee" value="<?= @$classfee ?>">
                <span class="text-danger"><?= @$messages['classfee'] ?></span>

            </div>

            <!-- Hall No -->
            <div class="form-group">
                <label for="hallno">Hall No</label>
                <input type="text" name="hallno" id="hallno" class="form-control">
                <span class="text-danger"><?= @$messages['hallno'] ?></span>
            </div>


        </div>

        <div class="card-footer  text-center">
            <input type="hidden" name="Id" id="Id" value="<?= $Id ?>">
            <button type="submit" name="action" value="update" class="btn btn-success">Update</button>
        </div>


    </form>
</div>
<?php
$content = ob_get_clean();
include '../layouts.php';
?>