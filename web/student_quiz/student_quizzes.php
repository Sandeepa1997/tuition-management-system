<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['STUDENT_ID'])) {
    header("Location: ../login.php");
    exit;
}

$db = dbConn();
$studentId = $_SESSION['STUDENT_ID'];

// Fetch subjects related to student’s enrolled classes
$sqlSubjects = "SELECT DISTINCT s.Id, s.name 
                FROM subjects s 
                JOIN classes c ON s.Id = c.Subject_id 
                JOIN student_enroll se ON c.Id = se.class_id 
                WHERE se.student_id = $studentId";
$resultSubjects = $db->query($sqlSubjects);

$subjects = [];
if ($resultSubjects->num_rows > 0) {
    while ($row = $resultSubjects->fetch_assoc()) {
        $subjects[$row['Id']] = $row['name'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Quizzes</title>
   
    <style>
        .quiz-title {
            font-weight: 500;
        }
        .icon {
            float: right;
            color: #0b64ebff;
        }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="mb-4 text-center">Available Quizzes</h2>

    <div class="row">
        <div class="col-lg-10 mx-auto">

            <?php if (!empty($subjects)) {
                foreach ($subjects as $subjectId => $subjectName) { ?>
                    <div class="card shadow mb-4">
                        <div class="card-header bg-success">
                            <h5 class="mb-0 text-white">
                                <?= htmlspecialchars($subjectName) ?>
                                <?php
                                // Fetch one quiz for this subject to show type and term
                                $quizInfoSql = "SELECT q.type, t.term FROM quizzes q
                                                JOIN classes c ON q.class_id = c.Id 
                                                JOIN terms t ON q.term_id = t.Id
                                                WHERE c.Subject_id = $subjectId 
                                                AND q.status='Ongoing'
                                                AND c.Id IN (SELECT class_id FROM student_enroll WHERE student_id = $studentId) 
                                                LIMIT 1";
                                $quizInfoResult = $db->query($quizInfoSql);
                                if ($quizInfoResult->num_rows > 0) {
                                    $info = $quizInfoResult->fetch_assoc();
                                    echo " - <small>{$info['term']} | {$info['type']}</small>";
                                }
                                ?>
                            </h5>
                        </div>
                        <div class="card-body">

                            <?php
                            $sqlQuizzes = "SELECT q.* FROM quizzes q
                                           JOIN classes c ON q.class_id = c.Id 
                                           WHERE c.Subject_id = $subjectId 
                                           AND c.Id IN (
                                               SELECT class_id FROM student_enroll WHERE student_id = $studentId AND q.status='Ongoing'
                                           )";
                            $resultQuizzes = $db->query($sqlQuizzes);

                            if ($resultQuizzes->num_rows > 0) { ?>
                                <div class="list-group">
                                    <?php while ($quiz = $resultQuizzes->fetch_assoc()) { ?>
                                        <a href="attempt_quiz.php?quiz_id=<?= $quiz['Id'] ?>" class="list-group-item list-group-item-action">
                                            <span class="quiz-title"><?= htmlspecialchars($quiz['title']) ?> </span>
                                            <i class="bi bi-arrow-right-circle-fill icon"></i>
                                        </a>
                                    <?php } ?>
                                </div>
                            <?php } else { ?>
                                <div class="alert alert-info text-center mb-0">
                                    No quizzes available for this subject.
                                </div>
                            <?php } ?>

                        </div>
                    </div>
                <?php }
            } else { ?>
                <div class="alert alert-warning text-center">
                    You are not enrolled in any classes yet.
                </div>
            <?php } ?>

        </div>
    </div>
</div>

</body>
</html>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
