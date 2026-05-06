<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['TEACHER_ID'])) {
    header("Location: ../login.php");
    exit;
}

$db = dbConn();
$teacherId = $_SESSION['TEACHER_ID'];
$selectedClassId = $_POST['class_id'] ?? '';
$selectedTermId = $_POST['term_id'] ?? '';
?>

<!-- Filter Form -->
<form method="post" class="mb-4">
    <div class="row">
        <div class="col-md-4">
            <label>Class:</label>
            <select name="class_id" class="form-control" required>
                <option value="">Select Class</option>
                <?php
                $sqlClasses = "SELECT Id, Class_Name FROM classes WHERE Teacher_id = $teacherId";
                $res = $db->query($sqlClasses);
                while ($row = $res->fetch_assoc()) {
                    $sel = ($row['Id'] == $selectedClassId) ? 'selected' : '';
                ?>
                    <option value="<?= $row['Id'] ?>" <?= $sel ?>><?= $row['Class_Name'] ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-md-4">
            <label>Term:</label>
            <select name="term_id" class="form-control" required>
                <option value="">Select Term</option>
                <?php
                $sqlTerms = "SELECT Id, term FROM terms";
                $resTerms = $db->query($sqlTerms);
                while ($row = $resTerms->fetch_assoc()) {
                    $sel = ($row['Id'] == $selectedTermId) ? 'selected' : '';
                ?>
                    <option value="<?= $row['Id'] ?>" <?= $sel ?>><?= $row['term'] ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">View Submissions</button>
        </div>
    </div>
</form>

<?php if ($selectedClassId && $selectedTermId): ?>

    <?php
    // Check if any assignment exists for the selected class and term
    $sqlCheckAssignment = "SELECT COUNT(*) AS cnt
                           FROM assignment_titles at
                           JOIN assignments a ON a.title_id = at.Id
                           WHERE at.class_id = '$selectedClassId' AND at.term_id = '$selectedTermId'";
    $resCheck = $db->query($sqlCheckAssignment);
    $rowCheck = $resCheck->fetch_assoc();

    if ($rowCheck['cnt'] == 0) {
        echo "<div class='alert alert-warning'>No assignments available for the selected term and class.</div>";
    } else {
        // Get due date
        $sqlDue = "SELECT a.due_date 
                   FROM assignment_titles at
                   JOIN assignments a ON a.title_id = at.Id
                   WHERE at.class_id = '$selectedClassId' AND at.term_id = '$selectedTermId'
                   ORDER BY a.due_date DESC LIMIT 1";
        $resDue = $db->query($sqlDue);
        $dueDateRow = $resDue->fetch_assoc();
        $dueDate = $dueDateRow ? $dueDateRow['due_date'] : null;
        $today = date('Y-m-d');
    ?>

        <form method="post" action="save_marks.php">
            <input type="hidden" name="term_id" value="<?= $selectedTermId ?>">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Reg No</th>
                        <th>Student Name</th>
                        <th>Submitted File</th>
                        <th>Marks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Get students
                    $sqlStudents = "SELECT s.Id as student_id, s.reg_no, u.FirstName, u.LastName 
                                    FROM students s
                                    JOIN users u ON s.userid = u.Id
                                    JOIN student_enroll se ON se.student_id = s.Id
                                    WHERE se.class_id = '$selectedClassId'";
                    $resStudents = $db->query($sqlStudents);

                    // Get submissions
                    $submissions = [];
                    $sqlSub = "SELECT a_sub.id AS submission_id, a_sub.student_id, a_sub.file_name, am.marks 
                               FROM assignment_submissions a_sub
                               LEFT JOIN assignments_marks am ON am.submission_id = a_sub.id
                               WHERE a_sub.term_id = '$selectedTermId'";
                    $resSub = $db->query($sqlSub);
                    while ($sub = $resSub->fetch_assoc()) {
                        $submissions[$sub['student_id']] = $sub;
                    }

                    if ($resStudents->num_rows > 0) {
                        while ($student = $resStudents->fetch_assoc()) {
                            $studentId = $student['student_id'];
                            $submitted = isset($submissions[$studentId]);
                            $bgrow = (!$submitted && $dueDate && $today > $dueDate) ? 'bg-danger' : '';
                    ?>
                            <tr class="<?= $bgrow ?>">
                                <td><?= $student['reg_no'] ?></td>
                                <td><?= $student['FirstName'] . ' ' . $student['LastName'] ?></td>
                                <td>
                                    <?php if ($submitted): ?>
                                        <a href='../../system/uploads/<?= $submissions[$studentId]['file_name'] ?>' target='_blank'>View File</a>
                                    <?php else: ?>
                                        Not Submitted
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($submitted): ?>
                                        <input type="hidden" name="submission_ids[]" value="<?= $submissions[$studentId]['submission_id'] ?>">
                                        <input type="hidden" name="student_ids[]" value="<?= $studentId ?>">
                                        <input type="text" name="marks[]" class="form-control"
                                               value="<?= isset($submissions[$studentId]['marks']) ? $submissions[$studentId]['marks'] : '' ?>">
                                    <?php else: ?>
                                        <input type="hidden" name="submission_ids[]" value="">
                                        <input type="hidden" name="student_ids[]" value="<?= $studentId ?>">
                                        <input type="text" class="form-control" value="null" readonly>
                                        <input type="hidden" name="marks[]" value="null">
                                    <?php endif; ?>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='4'>No students found for this class.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <button type="submit" class="btn btn-success">Save Status</button>
        </form>

    <?php } ?>
<?php endif; ?>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
