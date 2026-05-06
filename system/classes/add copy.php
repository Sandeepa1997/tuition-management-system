<?php
ob_start();
include '../../init.php';

//confirm whether login to the system
if (!isset($_SESSION['ID'])) {
    header("Location:../login.php");
}
?>

<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<?php
$db=dbConn();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    extract($_POST);

    $ClassName = dataclean($ClassName);
    $MaxStudents = dataclean($MaxStudents);
    $classfee = dataclean($classfee);

    $messages = array();

    // Empty Validation
    if (empty($ClassName)) {
        $messages['ClassName'] = "Please enter the class name!...";
    }
    if (empty($GradeLevel)) {
        $messages['GradeLevel'] = "Please enter the grade level!...";
    }
    if (empty($subject)) {
        $messages['subject'] = "Please enter the subject!...";
    }
    if (empty($Teacher_Name)) {
        $messages['Teacher_Name'] = "Please select the teacher!...";
    }
    if (empty($TimeSlot)) {
        $messages['TimeSlot'] = "Please select the time slot!...";
    }
    if (empty($classdate)) {
        $messages['classdate'] = "Please select the class date!...";
    }
    if (empty($month)) {
        $messages['month'] = "Please select the Month!...";
    }
    if (empty($MaxStudents)) {
        $messages['MaxStudents'] = "Please enter the maximum number of students!...";
    }
    if (empty($classfee)) {
        $messages['classfee'] = "Please enter the class fee!...";
    }

    if (empty($hallno)) {
        $messages['hallno'] = "Please enter the hall number!...";
    }

// Time conflict validation
if (!empty($start_time) && !empty($end_time) && !empty($classdate) && !empty($Teacher_Name) && !empty($hallno)) {
    $conflictSql = "SELECT * FROM classes 
                    WHERE Class_Date = '$classdate' 
                    AND (
                        (start_time <= '$start_time' AND end_time > '$start_time') OR
                        (start_time < '$end_time' AND end_time >= '$end_time') OR
                        ('$start_time' <= start_time AND '$end_time' >= end_time)
                    )
                    AND (
                        Teacher_id = '$Teacher_Name' OR
                        hall_no = '$hallno'
                    )";

    $resultConflict = $db->query($conflictSql);
    if ($resultConflict->num_rows > 0) {
        $messages['TimeSlot'] = "Time conflict: Another class overlaps with this time for the same teacher or hall.";
    }
}




    //Insert Query
    if (empty($messages)) {
        $sql = "INSERT INTO classes 
            (Class_Name, Grade_Level_id, Subject_id, Teacher_id, Class_Date,start_time,end_time,month, Maximum_Students, class_fee,hall_no)
            VALUES 
            ('$ClassName', '$GradeLevel', '$subject', '$Teacher_Name', '$classdate','$start_time','$end_time','$month','$MaxStudents', '$classfee','$hallno')";

        $db->query($sql);
        echo '<script>
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Class has been created successfully",
                    showConfirmButton: false,
                    timer: 3500
                }).then(function(){window.location="add.php"});
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
            <!-- Class Name -->
            <div class="form-group">
                <label for="ClassName">Class Name</label>
                <input type="text" class="form-control" name="ClassName" id="ClassName" placeholder="e.g., Grade 9 - Science">
                <span class="text-danger"><?= @$messages['ClassName'] ?></span>
            </div>

            <!-- Grade Level Dropdown -->
            <div class="form-group">
                <label for="grade-level">Grade-Level</label>
                <select name="GradeLevel" id="grade_id" class="form-control" onchange="loadTeachers()">
                    <option value="">--</option>
                    <?php
                    $db = dbConn();
                    $sql = "SELECT * FROM  grade_levels";
                    $result = $db->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                            <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>

                    <?php
                        }
                    }
                    ?>
                </select>
                <span class="text-danger"><?= @$messages['GradeLevel'] ?></span>


            </div>

            <!-- Subject Dropdown -->
            <div class="form-group">
                <label for="subject">Subject</label>
                <select name="subject" id="subject_id" class="form-control" onchange="loadTeachers()">
                    <option value="">--</option>
                    <?php
                    $db = dbConn();
                    $sql = "SELECT * FROM subjects";
                    $result = $db->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                            <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>

                    <?php
                        }
                    }
                    ?>
                </select>
                <span class="text-danger"><?= @$messages['subject'] ?></span>
            </div>


            <!-- Teacher Name -->
            <!-- Teacher -->
            <div class="form-group">
                <label for="Teacher_Name">Teacher</label>
                <select name="Teacher_Name" id="teacher_id" class="form-control">
                    <option value="">--</option>
                </select>
            </div>

            <span class="text-danger"><?= @$messages['Teacher_Name'] ?></span>



            <!-- start-time-->
            <div class="form-group mt-2">
                  <div class="form-group">
                <label for="start-time">Start-Time</label>
                <input type="time" name="start_time" id="start_time" class="form-control">
            </div>      
                <span class="text-danger"><?= @$messages['start_time'] ?></span>

          <!-- start-time-->
            <div class="form-group mt-2">
                  <div class="form-group">
                <label for="end-time">End-Time</label>
                <input type="time" name="end_time" id="end_time" class="form-control">
            </div>      
                <span class="text-danger"><?= @$messages['end_time'] ?></span>
                



                <!-- Class Date -->
                <div class="form-group mt-3">
                    <label for="">Class Date</label>
                    <select class="form-control" id="classdate" name="classdate">
                        <option value="">--</option>
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                    </select>
                    <span class="text-danger"><?= @$messages['classdate'] ?></span>
                </div>

                <div class="form-group mt-3">
                    <label for="">Month</label>
                    <select class="form-control" id="month" name="month">
                        <option value="">--</option>
                        <option value="Jan">January</option>
                        <option value="Feb">February</option>
                        <option value="March">March</option>
                        <option value="April">April</option>
                        <option value="May">May</option>
                        <option value="June">June</option>
                        <option value="July">July</option>
                        <option value="Aug">August</option>
                        <option value="Sep">September</option>
                        <option value="Oct">October</option>
                        <option value="Nov">November</option>
                        <option value="Dec">december</option>
                    </select>
                    <span class="text-danger"><?= @$messages['month'] ?></span>
                </div>


                <!-- Max Students -->
                <div class="form-group">
                    <label for="MaxStudents">Maximum Students</label>
                    <input type="number" class="form-control" name="MaxStudents" id="MaxStudents" min="1">
                    <span class="text-danger"><?= @$messages['MaxStudents'] ?></span>

                </div>

                <div class="form-group">
                    <label for="classfee">Class Fee</label>
                    <input type="text" class="form-control" name="classfee" id="classfee">
                    <span class="text-danger"><?= @$messages['classfee'] ?></span>

                </div>

                <div class="form-group">
                    <label for="classfee">Hall-No</label>
                    <input type="text" class="form-control" name="hallno" id="hallno">
                    <span class="text-danger"><?= @$messages['hallno'] ?></span>

                </div>


            </div>

            <!-- Submit Button -->
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Save Class</button>
            </div>

    </form>
</div>

<!-- Ajax section -->
<script>
    function loadTeachers() {

        var subjectId = $('#subject_id').val();
        var gradeId = $('#grade_id').val();

        if (subjectId && gradeId) {
            $.ajax({
                type: 'POST',
                url: 'get_teachers.php',
                data: {
                    sub_id: subjectId,
                    grade_id: gradeId
                },
                success: function(response) {

                    $('#teacher_id').html(response);
                }
            });
        } else {
            $('#teacher_id').html('<option value="">--</option>');
        }
    }
</script>
<!-- End -->




<?php
$content = ob_get_clean();
include '../layouts.php';
?>