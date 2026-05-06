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

// Initialize form variables
$class_id = $term = $date = $room = '';
$examId = null;
$hours = '';
$minutes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    extract($_POST);
    
    if ($action === 'edit') {
        $sql = "SELECT * FROM exams WHERE Id = '$Id'";
        $result = $db->query($sql);
        $row = $result->fetch_assoc();

        $class_id = $row['class_id'];
        $term = $row['term_id'];
        $time = $row['start_time'];
        $date = $row['date'];
        $room = $row['room_no'];
        $examId = $row['Id'];

        // Parse duration into hours and minutes
        $duration = $row['duration'];
        preg_match('/(\d+)\s*hour\s*:\s*(\d+)\s*minutes/', $duration, $matches);
        $hours = $matches[1] ?? '0';
        $minutes = $matches[2] ?? '0';
    }

    if ($action === 'update') {
        // Reconstruct duration
        $hour = str_pad((int)$hours, 2, '0', STR_PAD_LEFT);
        $minute = str_pad((int)$minutes, 2, '0', STR_PAD_LEFT);
        $duration = $hour . " hour : " . $minute . " minutes";

        $sql = "UPDATE exams 
                SET class_id = '$class_id', term_id = '$term', start_time = '$time', duration = '$duration', date = '$date', room_no = '$room' 
                WHERE Id = '$Id'";
        $db->query($sql);

        echo '<script>
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Exam updated successfully!",
                showConfirmButton: false,
                timer: 3000
            }).then(function(){window.location="view.php"});
        </script>';
        exit;
    }
}
?>

<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">Edit Exam</h3>
    </div>

    <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <div class="card-body">
            <input type="hidden" name="Id" value="<?= $examId ?>">
            <input type="hidden" name="subject_id" value="<?= $subjectId ?>">

            <div class="mb-3">
                <label>Select Class</label>
                <select name="class_id" class="form-control" required>
                    <option value="">-- Select Class --</option>
                    <?php while ($row = $resultClasses->fetch_assoc()) { ?>
                        <option value="<?= $row['class_id'] ?>" <?= $row['class_id'] == $class_id ? 'selected' : '' ?>>
                            <?= $row['grade_name'] ?> - <?= $row['subject_name'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="term" class="form-label">Term</label>
                <select class="form-control" id="term" name="term" required>
                    <option value="">Select the Term</option>
                    <?php
                    $sql = "SELECT * FROM terms";
                    $result = $db->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                        <option value="<?= $row['Id'] ?>" <?= $row['Id'] == $term ? 'selected' : '' ?>>
                            <?= $row['term'] ?>
                        </option>
                    <?php
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Start Time</label>
                <input type="text" name="time" class="form-control" value="<?= $time ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Duration</label>
                <div class="row g-2">
                    <div class="col-md-6">
                        <input type="number" name="hours" class="form-control" placeholder="Hours" min="0" value="<?= $hours ?>" required>
                    </div>
                    <div class="col-md-6">
                        <input type="number" name="minutes" class="form-control" placeholder="Minutes" min="0" max="59" value="<?= $minutes ?>" required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" value="<?= $date ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Room No</label>
                <input type="text" name="room" class="form-control" value="<?= $room ?>" required>
            </div>

            <div class="text-center mb-3">
                <button type="submit" name="action" value="update" class="btn btn-primary">Update</button>
            </div>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
