<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['TEACHER_ID'])) {
    header('Location: ../login.php');
    exit;
}

$db = dbConn();
$teacherId = $_SESSION['TEACHER_ID'];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $examId = $_POST['exam_id'];
    $status = $_POST['status'];

    $sqlUpdate = "UPDATE exams SET status='$status' WHERE Id = '$examId'";
    $db->query($sqlUpdate);
}

// Fetch exams with grade level
$sql ="SELECT e.Id AS exam_id, t.term, e.duration, e.date, c.Class_Name, e.status, s.Id AS subject_id, t.Id AS term_id, 
       g.name AS grade
FROM exams e
JOIN classes c ON e.class_id = c.Id
JOIN grade_levels g ON c.Grade_Level_id = g.Id
JOIN subjects s ON c.Subject_id = s.Id
JOIN terms t ON e.term_id = t.Id
WHERE c.Teacher_id = '$teacherId'
ORDER BY g.name ASC, s.Id ASC, t.Id DESC";

$result = $db->query($sql);

// Group exams by grade
$examsByGrade = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $grade = $row['grade'];
        $examsByGrade[$grade][] = $row;
    }
}
?>

<div class="card card-info">
    <div class="card-header bg-info text-white">
        <h3 class="card-title">Exam List</h3>
    </div>

    <div class="card-body table-responsive">
        <?php if (!empty($examsByGrade)) { ?>
            <?php foreach ($examsByGrade as $grade => $exams) { ?>
                <h5 class="mt-3 text-primary"><?= $grade ?></h5>
                <table class="table table-bordered table-hover text-center align-middle mb-4">
                    <thead class="table-light">
                        <tr>
                         
                            <th>Term</th>
                            <th>Duration</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th></th>
                            <th>Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($exams as $row) { ?>
                            <tr>
                         
                                <td><?= $row['term'] ?></td>
                                <td><?= $row['duration'] ?></td>
                                <td><?= $row['date'] ?></td>
                                <td>
                                    <form method="post" action="view.php">
                                        <input type="hidden" name="exam_id" value="<?= $row['exam_id'] ?>">
                                        <select name="status" class="form-select form-select-sm form-control">
                                            <option value="">Select Status</option>
                                            <option value="Scheduled" <?= $row['status'] == 'Scheduled' ? 'selected' : '' ?>>Scheduled</option>
                                            <option value="Completed" <?= $row['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                            <option value="Postponed" <?= $row['status'] == 'Postponed' ? 'selected' : '' ?>>Postponed</option>
                                            <option value="Cancelled" <?= $row['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                </td>
                                <td>
                                        <button type="submit" name="update_status" value="1" class="btn btn-sm btn-success">Proceed</button>
                                    </form>
                                </td>
                                <td>
                                    <form action="edit.php" method="post">
                                        <input type="hidden" name="Id" value="<?= $row['exam_id'] ?>">
                                        <button type="submit" name="action" value="edit" class="btn btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        <?php } else { ?>
            <p class="text-center">No exams found.</p>
        <?php } ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
