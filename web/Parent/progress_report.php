<?php
ob_start();
include '../../init.php';

$messages = array();
$regno = null;
$showReport = false;

$firstname = '';
$lastname = '';
$examAvg = $assignmentAvg = $quizAvg = null;
$finalGrade = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $regno = $_POST['regno'] ?? null;

    if (empty($regno)) {
        $messages['regno'] = "Please enter a Registration Number!";
    } else {
        $db = dbConn();

        // Get student
        $sqlStudent = "SELECT s.reg_no,s.guardian_id,s.Id,u.FirstName,u.LastName
        FROM students s
        INNER JOIN users u ON s.userid=u.Id
         WHERE s.reg_no = '$regno'";
        $resultStudent = $db->query($sqlStudent);

        if ($resultStudent->num_rows == 0) {
            $messages['regno'] = "No student found with this Registration Number!";
        } else {
            $studentRow = $resultStudent->fetch_assoc();
            $student_id = $studentRow['Id'];
            $firstname = $studentRow['FirstName'];
            $lastname = $studentRow['LastName'];
            $guardianId = $studentRow['guardian_id'];

            // Get logged-in parent NIC
            $loggedUserId = $_SESSION['ID'];
            $sqlNIC = "SELECT NIC_No FROM users WHERE Id = $loggedUserId";
            $resultNIC = $db->query($sqlNIC);

            if ($resultNIC->num_rows == 0) {
                $messages['regno'] = "User details not found!";
            } else {
                $nicNo = $resultNIC->fetch_assoc()['NIC_No'];

                // Get guardian NIC
                $sqlGuardianNIC = "SELECT u.NIC_No FROM parents p
                                   JOIN users u ON p.Userid = u.Id
                                   WHERE p.Id = $guardianId";
                $resultGuardian = $db->query($sqlGuardianNIC);

                if ($resultGuardian->num_rows == 0) {
                    $messages['regno'] = "Guardian data not found!";
                } else {
                    $guardianNIC = $resultGuardian->fetch_assoc()['NIC_No'];

                    if ($nicNo !== $guardianNIC) {
                        $messages['regno'] = "Please enter your own child's registration number!";
                    } else {
                        // Calculate marks
                        $examTotal = 0;
                        $examCount = 0;
                        $assignmentTotal = 0;
                        $assignmentCount = 0;
                        $quizTotalScore = 0;
                        $quizTotalQuestions = 0;
                        $quizCount = 0;

                        // Exams
                        $sqlexam = "SELECT er.marks FROM exam_results er 
                                    INNER JOIN exams e ON er.exam_id = e.Id 
                                    WHERE er.student_id = '$student_id'";
                        $resExam = $db->query($sqlexam);
                        while ($r = $resExam->fetch_assoc()) {
                            $examTotal += $r['marks'];
                            $examCount++;
                        }

                        // Assignments
                        $sqlAssignment = "SELECT am.marks FROM assignments_marks am
                                          INNER JOIN assignment_submissions asub ON am.submission_id = asub.Id
                                          WHERE asub.student_id = '$student_id'";
                        $resAssignment = $db->query($sqlAssignment);
                        while ($r = $resAssignment->fetch_assoc()) {
                            if (!is_null($r['marks'])) {
                                $assignmentTotal += $r['marks'];
                                $assignmentCount++;
                            }
                        }

                        // Quizzes
                        $sqlQuiz = "SELECT qa.score, qa.num_of_questions FROM quiz_attempts qa
                                    INNER JOIN (
                                        SELECT MAX(id) AS id FROM quiz_attempts
                                        WHERE student_id = '$student_id'
                                        GROUP BY quiz_id
                                    ) latest ON qa.id = latest.id";
                        $resQuiz = $db->query($sqlQuiz);
                        while ($r = $resQuiz->fetch_assoc()) {
                            $quizTotalScore += $r['score'];
                            $quizTotalQuestions += $r['num_of_questions'];
                            $quizCount++;
                        }

                        // Averages
                        $examAvg = $examCount > 0 ? round($examTotal / $examCount, 2) : 'Not Held';
                        $assignmentAvg = $assignmentCount > 0 ? round($assignmentTotal / $assignmentCount, 2) : 'Not Held';
                        $quizAvg = ($quizCount > 0 && $quizTotalQuestions > 0) ? round(($quizTotalScore / $quizTotalQuestions) * 100, 2) : 'Not Held';

                        // Final grade
                        if (is_numeric($examAvg) && is_numeric($assignmentAvg) && is_numeric($quizAvg)) {
                            $finalAvg = round(($examAvg + $assignmentAvg + $quizAvg) / 3, 2);
                            if ($finalAvg >= 75) $finalGrade = 'A';
                            elseif ($finalAvg >= 65) $finalGrade = 'B';
                            elseif ($finalAvg >= 55) $finalGrade = 'C';
                            elseif ($finalAvg >= 35) $finalGrade = 'S';
                            else $finalGrade = 'F';
                        } else {
                            $finalGrade = 'Incomplete';
                        }

                        $showReport = true;
                    }
                }
            }
        }
    }
}
?>

<div class="container mt-5">
    <h4 class="mb-4">View Child's Final Grade Summary</h4>

    <form method="post" class="p-4 border rounded shadow-sm bg-light mb-4">
        <div class="mb-3">
            <label for="regno" class="form-label fw-bold">Student Registration Number</label>
            <input type="text" name="regno" id="regno" class="form-control" placeholder="e.g., R123456" value="<?= htmlspecialchars($regno) ?>">
            <span class="text-danger" style="font-size: 13px;"><?= @$messages['regno'] ?></span>
        </div>
        <div class="text-end">
            <button type="submit" class="btn btn-primary px-4">Submit</button>
        </div>
    </form>

    <?php if ($showReport): ?>
        <div class="card border-info">
            <div class="card-header bg-info text-white fw-bold">
                Final Grade Summary - <?= $firstname ?> <?= $lastname ?>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Component</th>
                        <th>Average</th>
                    </tr>
                    <tr>
                        <td>Exam</td>
                        <td><?= is_numeric($examAvg) ? $examAvg : 'Not Held' ?></td>
                    </tr>
                    <tr>
                        <td>Assignment</td>
                        <td><?= is_numeric($assignmentAvg) ? $assignmentAvg : 'Not Held' ?></td>
                    </tr>
                    <tr>
                        <td>Quiz</td>
                        <td><?= is_numeric($quizAvg) ? $quizAvg . '%' : 'Not Held' ?></td>
                    </tr>
                    <tr style="background-color:#cce5ff;">
                        <td><strong>Final Status</strong></td>
                        <td><strong><?= $finalGrade ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
