<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['TEACHER_ID'])) {
    header('Location: ../login.php');
    exit;
}

$db = dbConn();

extract($_POST);
$teacherId = $_SESSION['TEACHER_ID'];

$selectedClassId = $class_id ?? null;
$selectedTermId = $term_id ?? null;
$selectedAssignmentId = $assignment_id ?? null;

// Fetch all classes for this teacher
$classSql = "SELECT Id, Class_Name FROM classes WHERE Teacher_id = $teacherId";
$classResult = $db->query($classSql);

// Fetch terms for the selected class
$termResult = null;
if ($selectedClassId) {
    $termSql = "SELECT DISTINCT t.Id, t.term
                FROM assignment_titles atl
                JOIN terms t ON atl.term_id = t.Id
                WHERE atl.class_id = $selectedClassId";
    $termResult = $db->query($termSql);
}

// Fetch assignments based on class and term
$assignmentResult = null;
if ($selectedClassId && $selectedTermId) {
    $assignmentSql = "SELECT a.Id AS assignment_id, atl.title, a.due_date
                      FROM assignments a
                      JOIN assignment_titles atl ON a.title_id = atl.Id
                      WHERE atl.class_id = $selectedClassId AND atl.term_id = $selectedTermId";
    $assignmentResult = $db->query($assignmentSql);
}

// Fetch assignment report
$assignmentInfo = null;
$submissionResults = null;
if ($selectedAssignmentId) {
    $infoQuery = "SELECT atl.title, t.term, c.Class_Name
                  FROM assignments a
                  JOIN assignment_titles atl ON a.title_id = atl.Id
                  JOIN classes c ON atl.class_id = c.Id
                  JOIN terms t ON atl.term_id = t.Id
                  WHERE a.Id = '$selectedAssignmentId'";
    $assignmentInfo = $db->query($infoQuery)->fetch_assoc();

    $submissionQuery = "SELECT s.reg_no, u.FirstName, u.LastName, am.marks
                        FROM student_enroll se
                        JOIN students s ON se.student_id = s.Id
                        JOIN users u ON s.userid = u.Id                   
                        LEFT JOIN assignment_submissions su 
                        ON su.student_id = s.Id AND su.assignment_id = '$selectedAssignmentId'
                        LEFT JOIN assignments_marks am ON am.submission_id = su.Id
                        WHERE se.class_id = (
                            SELECT atl.class_id 
                            FROM assignments a
                            JOIN assignment_titles atl ON a.title_id = atl.Id
                            WHERE a.Id = '$selectedAssignmentId')";


    $submissionResults = $db->query($submissionQuery);
}
?>

<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">Select Assignment Report</h3>
    </div>
    <div class="card-body">
        <form method="post" action="">
            <div class="form-group">
                <label for="class_id">Select Class</label>
                <select name="class_id" id="class_id" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Select Class --</option>
                    <?php if ($classResult->num_rows > 0) {
                        while ($row = $classResult->fetch_assoc()) { ?>
                            <option value="<?= $row['Id'] ?>" <?= ($selectedClassId == $row['Id']) ? 'selected' : '' ?>>
                                <?= $row['Class_Name'] ?>
                            </option>
                    <?php }
                    } ?>
                </select>
            </div>

            <?php if ($selectedClassId && $termResult && $termResult->num_rows > 0) { ?>
                <div class="form-group mt-3">
                    <label for="term_id">Select Term</label>
                    <select name="term_id" id="term_id" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Select Term --</option>
                        <?php while ($term = $termResult->fetch_assoc()) { ?>
                            <option value="<?= $term['Id'] ?>" <?= ($selectedTermId == $term['Id']) ? 'selected' : '' ?>>
                                <?= $term['term'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            <?php } ?>

            <?php if ($selectedClassId && $selectedTermId && $assignmentResult && $assignmentResult->num_rows > 0) { ?>
                <div class="form-group mt-3">
                    <label for="assignment_id">Select Assignment</label>
                    <select name="assignment_id" id="assignment_id" class="form-control" required>
                        <option value="">-- Select Assignment --</option>
                        <?php while ($a = $assignmentResult->fetch_assoc()) { ?>
                            <option value="<?= $a['assignment_id'] ?>" <?= ($selectedAssignmentId == $a['assignment_id']) ? 'selected' : '' ?>>
                                <?= $a['title'] ?> (Due: <?= $a['due_date'] ?>)
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-info mt-3">View Report</button>
            <?php } elseif ($selectedTermId) { ?>
                <p class="text-danger mt-3">No assignments found for this class and term.</p>
            <?php } ?>
        </form>
    </div>
</div>

<?php if ($selectedAssignmentId && $assignmentInfo) { ?>
    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <h4>Assignment Report: <?= $assignmentInfo['term'] ?> - <?= $assignmentInfo['Class_Name'] ?> - <?= $assignmentInfo['title'] ?></h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Reg No</th>
                        <th>Student Name</th>
                        <th>Marks</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($submissionResults->num_rows > 0) {
                        while ($row = $submissionResults->fetch_assoc()) {
                            $marks = $row['marks'] ?? null;
                            $rowClass = '';
                            if (is_null($row['marks'])) {
                                $marks = 'null';
                                $rowClass = 'style="background-color:rgba(235, 143, 39, 1);"';
                            } elseif ($row['marks'] >= 75) {
                                $marks = 'A';
                                $rowClass = 'bg-success';
                            } elseif ($row['marks'] >= 65) {
                                $marks = 'B';
                                $rowClass = 'style="background-color:rgba(109, 241, 57, 1);"';
                            } elseif ($row['marks'] >= 55) {
                                $marks = 'C';
                                $rowClass = 'style="background-color:rgba(142, 247, 100, 1);"';
                            } elseif ($row['marks'] >= 35) {
                                $marks = 'S';
                                $rowClass = 'bg-warning';
                            } else {
                                $marks = 'F';
                                $rowClass = 'bg-danger';
                            }
                    ?>
                            <tr <?= strpos($rowClass, 'style=') === 0 ? $rowClass : 'class="' . $rowClass . '"' ?>>

                                <td><?= $row['reg_no'] ?></td>
                                <td><?= $row['FirstName'] . ' ' . $row['LastName'] ?></td>
                                <td><?= $row['marks'] ?? '-' ?></td>
                                <td><?= $marks ?></td>
                            </tr>
                    <?php }
                    } else {
                        echo "<tr><td colspan='3' class='text-center'>No students found.</td></tr>";
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
<?php } ?>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>