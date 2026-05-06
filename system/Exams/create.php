<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['TEACHER_ID'])) {
    header('Location: ../login.php');
    exit;
}

$db = dbConn();
$teacherId = $_SESSION['TEACHER_ID'];

$messages = [];

$sqlSubject = "SELECT s.id AS subject_id FROM teachers t
               JOIN subjects s ON t.subject_id = s.id
               WHERE t.Id = '$teacherId' LIMIT 1";
$resultSubject = $db->query($sqlSubject);
$rowSubject = $resultSubject->fetch_assoc();
$subjectId = $rowSubject['subject_id'] ?? null;

$sqlClasses = "SELECT c.Id AS class_id, g.name AS grade_name, s.name AS subject_name
               FROM classes c
               JOIN grade_levels g ON c.Grade_Level_id = g.id
               JOIN subjects s ON c.Subject_id = s.id
               WHERE c.Teacher_id = '$teacherId'";
$resultClasses = $db->query($sqlClasses);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    extract($_POST);

    

    // Validate required fields
    if (empty($class_id)) $messages['class_id'] = "Please select a class!";
    if (empty($term)) $messages['term'] = "Please select a term!";
    if (empty($time)) $messages['time'] = "Please enter start time!";
    if (empty($hours) && empty($minutes)) $messages['duration'] = "Please specify exam duration!";
    if (empty($date)) $messages['date'] = "Please select a date!";
    if (empty($room)) $messages['room'] = "Please enter room number!";

    // Validate future date
    if (!empty($date)) {
        $today = date('Y-m-d');
        if ($date < $today) {
            $messages['date'] = "Exam date cannot be in the past!";
        }
    }

    if (empty($messages)) {
        $hour = str_pad((int)$hours, 2, '0', STR_PAD_LEFT);
        $minute = str_pad((int)$minutes, 2, '0', STR_PAD_LEFT);
        $duration = "$hour hour : $minute minutes";

        $sql = "INSERT INTO exams (class_id,term_id,start_time, duration, date, room_no)
                VALUES ('$class_id','$term','$time','$duration','$date','$room')";
        $db->query($sql);

        echo '<script>
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Exam created successfully!",
                showConfirmButton: false,
                timer: 3500
            }).then(function(){window.location="view.php"});
        </script>';
    }
}
?>

<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">Create Exam</h3>
    </div>

    <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <div class="card-body">
            <input type="hidden" name="subject_id" value="<?= $subjectId ?>">

            <!-- Class -->
            <div class="mb-3">
                <label>Select Class</label>
                <select name="class_id" class="form-control">
                    <option value="">-- Select Class --</option>
                    <?php while ($row = $resultClasses->fetch_assoc()) { ?>
                        <option value="<?= $row['class_id'] ?>" <?= @$class_id == $row['class_id'] ? 'selected' : '' ?>>
                            <?= $row['grade_name'] ?> - <?= $row['subject_name'] ?>
                        </option>
                    <?php } ?>
                </select>
                <span class="text-danger"><?= @$messages['class_id'] ?></span>
            </div>

            <!-- Term -->
            <div class="mb-3">
                <label for="term" class="form-label">Term</label>
                <select class="form-control" id="term" name="term">
                    <option value="">Select the Term</option>
                    <?php
                    $sql = "SELECT * FROM terms";
                    $result = $db->query($sql);
                    while ($row = $result->fetch_assoc()) {
                    ?>
                        <option value="<?= $row['Id'] ?>" <?= @$term == $row['Id'] ? 'selected' : '' ?>>
                            <?= $row['term'] ?>
                        </option>
                    <?php } ?>
                </select>
                <span class="text-danger"><?= @$messages['term'] ?></span>
            </div>

            <!-- Start Time -->
            <div class="mb-3">
                <label class="form-label">Start Time</label>
                <input type="time" name="time" class="form-control" value="<?= @$time ?>">
                <span class="text-danger"><?= @$messages['time'] ?></span>
            </div>

            <!-- Duration -->
            <div class="mb-3">
                <label class="form-label">Duration</label>
                <div class="row g-2">
                    <div class="col-md-6">
                        <input type="number" name="hours" class="form-control" placeholder="Hours" min="0" value="<?= @$hours ?>">
                    </div>
                    <div class="col-md-6">
                        <input type="number" name="minutes" class="form-control" placeholder="Minutes" min="0" max="59" value="<?= @$minutes ?>">
                    </div>
                </div>
                <span class="text-danger"><?= @$messages['duration'] ?></span>
            </div>

            <!-- Date -->
            <div class="mb-3">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" value="<?= @$date ?>">
                <span class="text-danger"><?= @$messages['date'] ?></span>
            </div>

            <!-- Room No -->
            <div class="mb-3">
                <label class="form-label">Room No</label>
                <input type="text" name="room" class="form-control" value="<?= @$room ?>">
                <span class="text-danger"><?= @$messages['room'] ?></span>
            </div>

            <!-- Submit -->
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
