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
$selectedQuizId = $quiz_id ?? null;

// Fetch teacher's classes
$classSql = "SELECT Id, Class_Name FROM classes WHERE Teacher_id = $teacherId";
$classResult = $db->query($classSql);

// Fetch available terms from quizzes table
$termResult = null;
if ($selectedClassId) {
    $termSql = "SELECT DISTINCT q.term_id, t.term 
                FROM quizzes q
                JOIN terms t ON q.term_id = t.Id
                WHERE q.class_id = $selectedClassId";
    $termResult = $db->query($termSql);
}

// Fetch quizzes
$quizResult = null;
if ($selectedClassId && $selectedTermId) {
    $quizSql = "SELECT Id, title FROM quizzes 
                WHERE class_id = $selectedClassId AND term_id = $selectedTermId";
    $quizResult = $db->query($quizSql);
}

// Fetch quiz info & scores
$quizInfo = null;
$attemptResults = null;
if ($selectedQuizId) {
    $infoQuery = "SELECT q.title, t.term, c.Class_Name
                  FROM quizzes q
                  JOIN terms t ON q.term_id = t.Id
                  JOIN classes c ON q.class_id = c.Id
                  WHERE q.Id = '$selectedQuizId'";
    $quizInfo = $db->query($infoQuery)->fetch_assoc();

    $submissionQuery = "SELECT qa.*, s.reg_no, u.FirstName, u.LastName
                        FROM quiz_attempts qa
                        JOIN (
                        SELECT student_id, MAX(id) AS latest_id
                        FROM quiz_attempts
                        WHERE quiz_id = '$selectedQuizId'
                        GROUP BY student_id)
                         latest_attempts ON qa.id = latest_attempts.latest_id
JOIN students s ON qa.student_id = s.Id
JOIN users u ON s.userid = u.Id
WHERE qa.quiz_id = '$selectedQuizId'";
 $attemptResults = $db->query($submissionQuery);
}
?>

<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">Select Quiz Report</h3>
    </div>
    <div class="card-body">
        <form method="post" action="">
            <div class="form-group">
                <label for="class_id">Select Class</label>
                <select name="class_id" id="class_id" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Select Class --</option>
                    <?php while ($row = $classResult->fetch_assoc()) { ?>
                        <option value="<?= $row['Id'] ?>" <?= ($selectedClassId == $row['Id']) ? 'selected' : '' ?>>
                            <?= $row['Class_Name'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <?php if ($termResult && $termResult->num_rows > 0) { ?>
                <div class="form-group mt-3">
                    <label for="term_id">Select Term</label>
                    <select name="term_id" id="term_id" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Select Term --</option>
                        <?php while ($term = $termResult->fetch_assoc()) { ?>
                            <option value="<?= $term['term_id'] ?>" <?= ($selectedTermId == $term['term_id']) ? 'selected' : '' ?>>
                                <?= $term['term'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            <?php } ?>

            <?php if ($quizResult && $quizResult->num_rows > 0) { ?>
                <div class="form-group mt-3">
                    <label for="quiz_id">Select Quiz</label>
                    <select name="quiz_id" id="quiz_id" class="form-control" required>
                        <option value="">-- Select Quiz --</option>
                        <?php while ($q = $quizResult->fetch_assoc()) { ?>
                            <option value="<?= $q['Id'] ?>" <?= ($selectedQuizId == $q['Id']) ? 'selected' : '' ?>>
                                <?= $q['title'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-info mt-3">View Report</button>
            <?php } elseif ($selectedTermId) { ?>
                <p class="text-danger mt-3">No quizzes found for this class and term.</p>
            <?php } ?>
        </form>
    </div>
</div>

<?php if ($selectedQuizId && $quizInfo) { ?>
    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <h4>Quiz Report: <?= $quizInfo['term'] ?> - <?= $quizInfo['Class_Name'] ?> - <?= $quizInfo['title'] ?></h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Reg No</th>
                        <th>Student Name</th>
                        <th>Percentage%</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($attemptResults && $attemptResults->num_rows > 0) {
                        while ($row = $attemptResults->fetch_assoc()) {
                         $percentage = round($row['percentage'], 2);

                            $rowClass = '';

                            if (is_null($percentage)) {
                                $grade = 'Not Submitted';
                                $rowClass = 'style="background-color:rgba(235, 143, 39, 1);"';
                            } elseif ($percentage >= 75) {
                                $grade = 'A';
                                $rowClass = 'bg-success';
                            } elseif ($percentage >= 65) {
                                $grade = 'B';
                                $rowClass = 'style="background-color:rgba(109, 241, 57, 1);"';
                            } elseif ($percentage >= 55) {
                                $grade = 'C';
                                $rowClass = 'style="background-color:rgba(142, 247, 100, 1);"';
                            } elseif ($percentage >= 35) {
                                $grade = 'S';
                                $rowClass = 'bg-warning';
                            } else {
                                $grade = 'F';
                                $rowClass = 'bg-danger';
                            }
                    ?>
                            <tr <?= strpos($rowClass, 'style=') === 0 ? $rowClass : 'class="' . $rowClass . '"' ?>>
                                <td><?= $row['reg_no'] ?></td>
                                <td><?= $row['FirstName'] . ' ' . $row['LastName'] ?></td>
                             <td> <?= number_format($row['percentage'], 2);?>%</td>
                                <td><?= $grade ?></td>
                            </tr>
                    <?php }
                    } else {
                        echo "<tr><td colspan='3' class='text-center'>No records found.</td></tr>";
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