<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['TEACHER_ID'])) {
    header('Location: ../login.php');
    exit;
}

$db = dbConn();
$teacherId = $_SESSION['TEACHER_ID'];

$selectedClassId = isset($_POST['class_id']) ? $_POST['class_id'] : null;
$selectedExamId = isset($_POST['mark_exam_id']) ? $_POST['mark_exam_id'] : null;

// Fetch all classes for this teacher
$classSql = "SELECT Id, Class_Name FROM classes WHERE Teacher_id = $teacherId";
$classResult = $db->query($classSql);

// Fetch exams only if class is selected
$examResult = null;
if ($selectedClassId) {
    $examSql = "SELECT e.Id AS exam_id, t.term, t.Id AS term_id, e.date
                FROM exams e
                JOIN terms t ON e.term_id = t.Id 
                WHERE e.class_id = $selectedClassId AND e.status = 'Completed'";
    $examResult = $db->query($examSql);
}

// Fetch exam info and marks if exam is selected
$examInfo = null;
$marksResult = null;
if ($selectedExamId) {
    $examInfoQuery = "SELECT t.term,t.Id AS term_id, c.Class_Name,e.date
                      FROM exams e 
                      JOIN classes c ON e.class_id = c.Id 
                      JOIN terms t ON e.term_id = t.Id
                      WHERE e.Id = '$selectedExamId'";
    $examInfo = $db->query($examInfoQuery)->fetch_assoc();

    $marksQuery = "SELECT s.reg_no, u.FirstName, u.LastName, er.marks
                   FROM exam_results er
                   JOIN students s ON er.student_id = s.Id
                   JOIN users u ON s.userid = u.Id
                   WHERE er.exam_id = '$selectedExamId'
                   ORDER BY 
                   CASE WHEN er.marks IS NULL THEN 1 ELSE 0 END, 
                   er.marks DESC";
    $marksResult = $db->query($marksQuery);
}
?>

<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">View Exam Report</h3>
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

            <?php if ($selectedClassId && $examResult && $examResult->num_rows > 0) { ?>
                <div class="form-group mt-3">
                    <label for="mark_exam_id">Select Completed Exam</label>
                    <select name="mark_exam_id" id="mark_exam_id" class="form-control" required>
                        <option value="">-- Select Exam --</option>
                        <?php while ($exam = $examResult->fetch_assoc()) { ?>
                            <option value="<?= $exam['exam_id'] ?>" <?= ($selectedExamId == $exam['exam_id']) ? 'selected' : '' ?>>
                                <?= $exam['term'] ?> - <?= $exam['date'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-success mt-3">View Report</button>
            <?php } elseif ($selectedClassId) { ?>
                <p class="text-danger mt-3">No completed exams found for this class.</p>
            <?php } ?>
        </form>
    </div>
</div>

<?php if ($selectedExamId && $examInfo) { ?>
    <div class="card card-info mt-4">
        <div class="card-header">
            <h3 class="card-title fw-bold">Exam Report: <?= $examInfo['term'] ?> - <?= $examInfo['Class_Name'] ?> - <?= $examInfo['date'] ?></h3>
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
                    <?php
                    if ($marksResult->num_rows > 0) {
                        while ($row = $marksResult->fetch_assoc()) {
                            $status = '';
                            $rowClass = '';

                            if (($row['marks'])=='null') {
                                $status = 'Absent';
                                $rowClass = 'style="background-color:rgba(235, 143, 39, 1);"';
                            } elseif ($row['marks'] >= 75) {
                                $status = 'A';
                                $rowClass = 'bg-success';
                            } elseif ($row['marks'] >= 65) {
                                $status = 'B';
                                $rowClass = 'style="background-color:rgb(116, 245, 66);"';
                            } elseif ($row['marks'] >= 55) {
                                $status = 'C';
                                $rowClass = 'style="background-color:rgb(116, 245, 66);"';
                            } elseif ($row['marks'] >= 35) {
                                $status = 'S';
                                $rowClass = 'bg-warning';
                            } else {
                                $status = 'F';
                                $rowClass = 'bg-danger';
                            }
                    ?>
                            <tr <?= strpos($rowClass, 'style=') === 0 ? $rowClass : 'class="' . $rowClass . '"' ?>>
                                <td><?= $row['reg_no'] ?></td>
                                <td><?= $row['FirstName'] . ' ' . $row['LastName'] ?></td>
                                <td><?= $row['marks'] =='null'?'-':$row['marks']?></td>
                                <td><?= $status ?></td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center'>No results found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
<?php } ?>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
