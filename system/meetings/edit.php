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



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);
    
    if ($action == 'edit') {
        $sql = "SELECT * FROM meetings WHERE Id = '$Id'";
        $result = $db->query($sql);
        $row = $result->fetch_assoc();

        $class_id = $row['class_id'];
        $type = $row['type'];
        $start_time = $row['start_time'];
        $end_time = $row['end_time'];
        $date = $row['date'];
       

    }

    if ($action == 'update') {
       extract($_POST);
       $Id=$_POST['meeting_id'];

      $sql = "UPDATE meetings 
                SET class_id = '$class_id', type = '$type', start_time= '$start_time ', end_time = '$end_time', date = '$date' 
                WHERE Id = '$Id'";
        $db->query($sql);

        echo '<script>
            Swal.fire({
                position: "center",
                icon: "success",
                title: "meeting updated successfully!",
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
        <h3 class="card-title">Edit Meeting</h3>
    </div>

   

    <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
    <div class="card-body">
        <input type="hidden" name="meeting_id" value="<?= $row['Id'] ?>">
        <input type="hidden" name="subject_id" value="<?= $subjectId ?>">

        <div class="mb-3">
            <label>Select Class</label>
            <select name="class_id" class="form-control">
                <option value="">-- Select Class --</option>
                <?php while ($classRow = $resultClasses->fetch_assoc()) { ?>
                    <option value="<?= $classRow['class_id'] ?>"
                        <?= ($classRow['class_id'] == $row['class_id']) ? 'selected' : '' ?>>
                        <?= $classRow['grade_name'] ?> - <?= $classRow['subject_name'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Select a Type</label>
            <select name="type" id="type" class="form-control">
                <option value="">--</option>
                <option value="Monthly Meeting" <?= ($row['type'] == 'Monthly Meeting') ? 'selected' : '' ?>>Monthly Meeting</option>
                <option value="Progress Meeting" <?= ($row['type'] == 'Progress Meeting') ? 'selected' : '' ?>>Progress Meeting</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Start Time</label>
            <input type="time" name="start_time" class="form-control" value="<?= $row['start_time'] ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">End Time</label>
            <input type="time" name="end_time" class="form-control" value="<?= $row['end_time'] ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" name="date" class="form-control" value="<?= $row['date'] ?>">
        </div>

        <div class="text-center mb-3">
            <button type="submit" class="btn btn-success" name="action" value="update">Update Meeting</button>

        </div>
    </div>
</form>


</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
