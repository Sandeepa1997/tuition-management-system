<?php
ob_start();
include '../../init.php';
extract($_POST);

?>
<div class="container">
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $db = dbConn();

        $sql = "SELECT * FROM students s INNER JOIN users u ON s.userid=u.Id WHERE s.Id='$student_id'";
        $result = $db->query($sql);
        $row = $result->fetch_assoc();
        $firstname = $row['FirstName'];
        $lastname = $row['LastName'];

    ?>
        <h1><?= $firstname ?> <?= $lastname ?> </h1>

        <?php
        $sqlclass = "SELECT c.Class_Name,c.Id FROM classes c
INNER JOIN student_enroll se ON c.Id = se.class_id

 WHERE se.student_id='$student_id'";

        $result = $db->query($sqlclass);

        while ($row = $result->fetch_assoc()) {
        ?>
            <div class="card">
                <div class="card-header">

                    <?= $row['Class_Name'] ?>
                </div>
                <div class="card-body">


                    <?php


                    $classId = $row['Id'];

                    echo '<br>';

                    $sqlterm = "SELECT* FROM terms";
                    $resultterm = $db->query($sqlterm);
                    ?>
                    <table class=" table table-stripped">
                        <tr>
                            <th>Term</th>
                            <th>Marks</th>
                        </tr>

                        <?php
                        $total = 0;
                        while ($rowterm = $resultterm->fetch_assoc()) {

                        ?>
                            <tr>
                                <?php
                                ?>
                                <td><?= $rowterm['term'] ?></td>

                                <?php

                                $termId = $rowterm['Id'];
                                echo '<br>';

                                $sqlexam = "SELECT * FROM exam_results er
            INNER JOIN exams e ON er.exam_id=e.Id
              WHERE student_id ='$student_id' AND e.class_id= '$classId' AND er.term_id='$termId'";

                                $resultexam = $db->query($sqlexam);
                                while ($rowexam = $resultexam->fetch_assoc()) {
                                    $total += $rowexam['marks'];
                                ?>
                                    <td><?= $rowexam['marks'] ?></td>
                                <?php

                                }
                                ?>
                            </tr>

                        <?php
                        }
                        ?>
                        <tr>
                            <td>Avg Marks</td>
                            <td><?=round($total /3,2)?></td>
                        </tr>
                    </table>
                    <?php

                    ?>
                </div>
            </div>
        <?php
        }
        ?>

    <?php

    }
    ?>
</div>

<!--Assignment Final Report-->
<div class="container mt-4">
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $db = dbConn();

        $sql = "SELECT * FROM students s INNER JOIN users u ON s.userid=u.Id WHERE s.Id='$student_id'";
        $result = $db->query($sql);
        $row = $result->fetch_assoc();
        $firstname = $row['FirstName'];
        $lastname = $row['LastName'];
    ?>
        <h1><?= $firstname ?> <?= $lastname ?> - Assignment Final Report</h1>

        <?php
        $sqlclass = "SELECT c.Class_Name,c.Id FROM classes c
                    INNER JOIN student_enroll se ON c.Id = se.class_id
                    WHERE se.student_id='$student_id'";
        $result = $db->query($sqlclass);

        while ($row = $result->fetch_assoc()) {
        ?>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <?= $row['Class_Name'] ?>
                    <?= date('Y') ?>
                </div>
                <div class="card-body">
                    <?php
                    $classId = $row['Id'];
                    $sqlterm = "SELECT * FROM terms";
                    $resultterm = $db->query($sqlterm);
                    ?>
                    <table class="table table-bordered">
                        <tr class="bg-dark text-white">
                            <th>Term</th>
                            <th>Assignment Title</th>
                            <th>Marks</th>
                            <th>Status</th>
                        </tr>

                        <?php
                        $totalMarks = 0;
                        $assignmentCount = 0;

                        while ($rowterm = $resultterm->fetch_assoc()) {
                            $termId = $rowterm['Id'];

                            $sqlassignment = "SELECT a.title_id, at.title,
                                                asub.submitted_at, asub.Id AS submission_id,
                                                am.marks
                                                FROM assignments a
                                                INNER JOIN assignment_titles at ON a.title_id = at.Id
                                                LEFT JOIN assignment_submissions asub ON a.Id = asub.assignment_id AND asub.student_id = '$student_id'
                                                LEFT JOIN assignments_marks am ON asub.Id = am.submission_id
                                                WHERE at.class_id = '$classId' AND at.term_id = '$termId'
                                                ORDER BY a.due_date";

                            $resultassignment = $db->query($sqlassignment);

                            if ($resultassignment->num_rows > 0) {
                        ?>
                                <tr class="table-secondary">
                                    <td colspan="4"><strong><?= $rowterm['term'] ?></strong></td>
                                </tr>

                                <?php
                                while ($rowassignment = $resultassignment->fetch_assoc()) {
                                    $assignmentCount++;
                                    $mark = $rowassignment['marks'];
                                    $status = '';
                                    $rowClass = '';

                                    if (is_null($mark)) {
                                        $status = 'Absent';
                                        $rowClass = 'style="background-color:rgba(235, 143, 39, 1);"';
                                    } elseif ($mark >= 75) {
                                        $status = 'A';
                                        $rowClass = 'class="bg-success text-white"';
                                        $totalMarks += $mark;
                                    } elseif ($mark >= 65) {
                                        $status = 'B';
                                        $rowClass = 'style="background-color:rgb(116, 245, 66);"';
                                        $totalMarks += $mark;
                                    } elseif ($mark >= 55) {
                                        $status = 'C';
                                        $rowClass = 'style="background-color:rgb(116, 245, 66);"';
                                        $totalMarks += $mark;
                                    } elseif ($mark >= 35) {
                                        $status = 'S';
                                        $rowClass = 'class="bg-warning"';
                                        $totalMarks += $mark;
                                    } else {
                                        $status = 'F';
                                        $rowClass = 'class="bg-danger text-white"';
                                        $totalMarks += $mark;
                                    }
                                ?>
                                    <tr <?= $rowClass ?>>
                                        <td></td>
                                        <td><?= $rowassignment['title'] ?></td>
                                        <td><?= is_null($mark) ? '-' : $mark ?></td>
                                        <td><?= $status ?></td>
                                    </tr>
                        <?php
                                }
                            }
                        }
                        ?>
                        <tr class="font-weight-bold bg-light">
                            <td colspan="2">Total Assignments</td>
                            <td colspan="2"><?= $assignmentCount ?></td>
                        </tr>
                        <tr class="font-weight-bold bg-light">
                            <td colspan="2">Average Marks</td>
                            <td colspan="2">
                                <?= $assignmentCount > 0 ? round($totalMarks / $assignmentCount, 2) : 'N/A' ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        <?php
        }
        ?>
    <?php
    }
    ?>
</div>

<!--Quiz Final Report-->
<div class="container">
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $db = dbConn();

        $sql = "SELECT * FROM students s INNER JOIN users u ON s.userid=u.Id WHERE s.Id='$student_id'";
        $result = $db->query($sql);
        $row = $result->fetch_assoc();
        $firstname = $row['FirstName'];
        $lastname = $row['LastName'];
    ?>
        <h1><?= $firstname ?> <?= $lastname ?> - Quiz Final Report <?= date('Y') ?></h1>

        <?php
        $sqlclass = "SELECT c.Class_Name,c.Id FROM classes c 
                     INNER JOIN student_enroll se ON c.Id = se.class_id 
                     WHERE se.student_id='$student_id'";
        $result = $db->query($sqlclass);

        while ($row = $result->fetch_assoc()) {
        ?>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <?= $row['Class_Name'] ?>
                </div>
                <div class="card-body">
                    <?php
                    $classId = $row['Id'];
                    $sqlterm = "SELECT * FROM terms";
                    $resultterm = $db->query($sqlterm);
                    ?>
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Term</th>
                                <th>Quiz Title</th>
                                <th>Questions</th>
                                <th>Score</th>
                                <th>Percentage</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalScore = 0;
                            $totalQuestions = 0;
                            $quizCount = 0;

                            while ($rowterm = $resultterm->fetch_assoc()) {
                                $termId = $rowterm['Id'];

                                $sqlquiz = "SELECT q.Id as quiz_id, q.title, q.status,
                                            qa.score, qa.percentage, qa.num_of_questions
                                            FROM quizzes q
                                            LEFT JOIN (
                                                SELECT * FROM quiz_attempts 
                                                WHERE student_id = '$student_id'
                                                AND id IN (
                                                    SELECT MAX(id) FROM quiz_attempts 
                                                    WHERE student_id = '$student_id'
                                                    GROUP BY quiz_id
                                                )
                                            ) qa ON q.Id = qa.quiz_id
                                            WHERE q.class_id = '$classId' AND q.term_id = '$termId'
                                            ORDER BY q.Id";

                                $resultquiz = $db->query($sqlquiz);

                                if ($resultquiz->num_rows > 0) {
                            ?>
                                    <tr class="table-secondary">
                                        <td colspan="6"><strong><?= $rowterm['term'] ?></strong></td>
                                    </tr>
                                    <?php
                                    while ($rowquiz = $resultquiz->fetch_assoc()) {
                                        $quizCount++;
                                        $score = $rowquiz['score'];
                                        $percentage = $rowquiz['percentage'];
                                        $questions = $rowquiz['num_of_questions'];


                                        $status = '';
                                        $rowClass = '';

                                        if (is_null($percentage)) {
                                            $status = 'Absent';
                                            $rowClass = 'style="background-color:rgba(235, 143, 39, 1);"';
                                        } elseif ($percentage >= 75) {
                                            $status = 'A';
                                            $rowClass = 'class="bg-success"';
                                            $totalScore += $score;
                                            $totalQuestions += $questions;
                                        } elseif ($percentage >= 65) {
                                            $status = 'B';
                                            $rowClass = 'style="background-color:rgb(116, 245, 66);"';
                                            $totalScore += $score;
                                            $totalQuestions += $questions;
                                        } elseif ($percentage >= 55) {
                                            $status = 'C';
                                            $rowClass = 'style="background-color:rgb(116, 245, 66);"';
                                            $totalScore += $score;
                                            $totalQuestions += $questions;
                                        } elseif ($percentage >= 35) {
                                            $status = 'S';
                                            $rowClass = 'class="bg-warning"';
                                            $totalScore += $score;
                                            $totalQuestions += $questions;
                                        } else {
                                            $status = 'F';
                                            $rowClass = 'class="bg-danger"';
                                            $totalScore += $score;
                                            $totalQuestions += $questions;
                                        }

                                    ?>
                                        <tr <?= $rowClass ?>>
                                            <td></td>
                                            <td><?= $rowquiz['title'] ?></td>
                                            <td><?= $questions ?? 'N/A' ?></td>
                                            <td><?= $score ?? 'Not Attempted' ?></td>
                                            <td><?= $percentage !== null ? $percentage . '%' : 'N/A' ?></td>
                                            <td><?= $status ?></td>
                                        </tr>
                            <?php
                                    }
                                }
                            }
                            ?>
                            <tr class="font-weight-bold bg-light">
                                <td colspan="2">Total Quizzes</td>
                                <td><?= $quizCount ?></td>
                                <td><?= $totalScore ?></td>
                                <td><?= $totalQuestions > 0 ? round(($totalScore / $totalQuestions) * 100, 2) . '%' : 'N/A' ?></td>
                                <td></td>
                            </tr>
                            <tr class="font-weight-bold bg-light">
                                <td colspan="2">Average Quiz Score</td>
                                <td></td>
                                <td><?= $quizCount > 0 ? round($totalScore / $quizCount, 2) : 'N/A' ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php
        }
        ?>
    <?php
    }
    ?>
</div>


<!-- Final Grade Summary -->
<div class="container mt-4">
    <h2>Final Grade Summary - <?= $firstname ?> <?= $lastname ?></h2>
    <?php
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
    $quizAvg = $quizCount > 0 && $quizTotalQuestions > 0 ? round(($quizTotalScore / $quizTotalQuestions) * 100, 2) : 'Not Held';

    // Final grade
    if (is_numeric($examAvg) && is_numeric($assignmentAvg) && is_numeric($quizAvg)) {
        $finalAvg = round(($examAvg + $assignmentAvg + $quizAvg) / 3, 2);
        if ($finalAvg >= 75) {
            $finalGrade = 'A';
        } elseif ($finalAvg >= 65) {
            $finalGrade = 'B';
        } elseif ($finalAvg >= 55) {
            $finalGrade = 'C';
        } elseif ($finalAvg >= 35) {
            $finalGrade = 'S';
        } else {
            $finalGrade = 'F';
        }
    } else {
        $finalGrade = 'Incomplete';
    }
    ?>
    <table class="table table-bordered mt-3">
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







<?php
$content = ob_get_clean();
include '../layouts.php';
?>