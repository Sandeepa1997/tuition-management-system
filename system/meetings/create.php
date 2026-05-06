<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['TEACHER_ID'])) {
    header('Location: ../login.php');
    exit;
}

$db = dbConn();
$teacherId = $_SESSION['TEACHER_ID'];

// Get subject ID
$sqlSubject = "SELECT s.id AS subject_id FROM teachers t
               JOIN subjects s ON t.subject_id = s.id
               WHERE t.Id = '$teacherId' LIMIT 1";
$resultSubject = $db->query($sqlSubject);
$rowSubject = $resultSubject->fetch_assoc();
$subjectId = $rowSubject['subject_id'] ?? null;

// Get class list
$sqlClasses = "SELECT c.Id AS class_id, g.name AS grade_name, s.name AS subject_name
               FROM classes c
               JOIN grade_levels g ON c.Grade_Level_id = g.id
               JOIN subjects s ON c.Subject_id = s.id
               WHERE c.Teacher_id = '$teacherId'";
$resultClasses = $db->query($sqlClasses);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    extract($_POST);
    
    // Clean the data
    $class_id = dataclean($class_id);
    $type = dataclean($type);
    $start_time = dataclean($start_time);
    $end_time = dataclean($end_time);
    $date = dataclean($date);

    $messages = [];

    // Required field validations
    if (empty($class_id)) $messages['class_id'] = "Please Select a class!!!";
    if (empty($type)) $messages['type'] = "Please select a meeting type!!!";
    if (empty($start_time)) $messages['start_time'] = "Please enter a start time!!!";
    if (empty($end_time)) $messages['end_time'] = "Please enter an end time!!!";
    if (empty($date)) $messages['date'] = "Please enter a date!!!";

    // Date validation - cannot select past dates
    if (!empty($date)) {
        $meetingDate = new DateTime($date);
        $today = new DateTime();
        $today->setTime(0, 0, 0); // Set time to start of day for comparison
        
        if ($meetingDate < $today) {
            $messages['date'] = "Meeting date cannot be in the past!";
        }
    }

    // Time validation - start time must be earlier than end time
    if (!empty($start_time) && !empty($end_time)) {
        $startTime = new DateTime($start_time);
        $endTime = new DateTime($end_time);
        
        if ($startTime >= $endTime) {
            $messages['start_time'] = "Start time must be earlier than end time!";
        }
    }

    // If no errors, insert into DB
    if (empty($messages)) {
        $sql = "INSERT INTO meetings(class_id, type, teacher_id, start_time, end_time, date, status) 
                VALUES ('$class_id','$type','$teacherId','$start_time','$end_time','$date','Scheduled')";
        $db->query($sql);

        echo '<script>
          Swal.fire({
            position: "center",
            icon: "success",
            title: "Your work has been saved",
            showConfirmButton: false,
            timer: 3500
          }).then(function(){window.location="view.php"});
        </script>';
        exit();
    }
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">Create a Meeting</h3>
    </div>

    <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" novalidate>
        <div class="card-body">
            <input type="hidden" name="subject_id" value="<?= $subjectId ?>">

            <div class="mb-3">
                <label class="fw-bold">Select Class</label>
                <select name="class_id" class="form-control">
                    <option value="">-- Select Class --</option>
                    <?php 
                    // Reset the result pointer to reuse the query
                    $resultClasses = $db->query($sqlClasses);
                    while ($row = $resultClasses->fetch_assoc()) { ?>
                        <option value="<?= $row['class_id'] ?>" <?php if (isset($class_id) && $class_id == $row['class_id']) echo 'selected'; ?>>
                            <?= $row['grade_name'] ?> - <?= $row['subject_name'] ?>
                        </option>
                    <?php } ?>
                </select>
                <span class="text-danger" style="font-size: 13px;"><?= @$messages['class_id'] ?></span>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Select a Type</label>
                <select name="type" id="type" class="form-control">
                    <option value="">--</option>
                    <option value="Monthly Meeting" <?php if (isset($type) && $type == 'Monthly Meeting') echo 'selected'; ?>>Monthly Meeting</option>
                    <option value="Progress Meeting" <?php if (isset($type) && $type == 'Progress Meeting') echo 'selected'; ?>>Progress Meeting</option>
                </select>
                <span class="text-danger" style="font-size: 13px;"><?= @$messages['type'] ?></span>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Start Time</label>
                <input type="time" name="start_time" class="form-control" value="<?= @$start_time ?>">
                <span class="text-danger" style="font-size: 13px;"><?= @$messages['start_time'] ?></span>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">End Time</label>
                <input type="time" name="end_time" class="form-control" value="<?= @$end_time ?>">
                <span class="text-danger" style="font-size: 13px;"><?= @$messages['end_time'] ?></span>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Date</label>
                <input type="date" name="date" class="form-control" value="<?= @$date ?>">
                <span class="text-danger" style="font-size: 13px;"><?= @$messages['date'] ?></span>
            </div>

            <div class="text-center mb-3">
                <button type="submit" class="btn btn-primary">Proceed</button>
            </div>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>