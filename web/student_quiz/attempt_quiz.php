<?php
ob_start();
include '../../init.php';

// Check student login
if (!isset($_SESSION['STUDENT_ID'])) {
    header("Location: ../login.php");
    exit;
}

$db = dbConn();
$studentId = $_SESSION['STUDENT_ID'];
$quizId = $_GET['quiz_id'] ?? null;



// Fetch quiz
$sqlQuiz = "SELECT * FROM quizzes WHERE Id = $quizId";
$resultQuiz = $db->query($sqlQuiz);
if ($resultQuiz->num_rows == 0) {
    echo "Quiz not found.";
    exit;
}
$quiz = $resultQuiz->fetch_assoc();

// Check attempts
$sqlAttempts = "SELECT COUNT(*) AS attempt_count FROM quiz_attempts WHERE student_id = $studentId AND quiz_id = $quizId";
$resultAttempts = $db->query($sqlAttempts);
$rowAttempts = $resultAttempts->fetch_assoc();
$currentAttempts = $rowAttempts['attempt_count'];
$maxAttempts = $quiz['attempts'];


if ($currentAttempts >= $maxAttempts) {
    ?>
    <div class="container py-4">
        <div class="alert alert-danger d-flex align-items-center shadow-sm rounded" role="alert">
            <i class="bi bi-x-circle-fill me-2 fs-4"></i>
            <div>
                <strong>You’ve reached the <u>maximum number of attempts</u> for this quiz.</strong>
            </div>
        </div>
    </div>
    <?php
    $content = ob_get_clean();
    include '../layouts.php';
    exit;
}
?>


<?php

// Fetch questions
$sqlQuestions = "SELECT * FROM quiz_questions WHERE quiz_id = $quizId";
$resultQuestions = $db->query($sqlQuestions);
?>



<head>
   
    <title><?= htmlspecialchars($quiz['title']) ?> - Attempt Quiz</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <style>
        .question-card {
            border: 1px solidrgb(47, 66, 85);
            border-radius: .5rem;
            padding: 20px;
            margin-bottom: 25px;
            background-color: rgb(148, 205, 209);
        }

        .question-number {
            font-weight: bold;
            color: rgb(11, 11, 12);
        }

        .card-body {
            background-color: rgb(148, 205, 209);
        }
    </style>
</head>

<body class="bg-light">

    <div class="container">
        
            <div class="card shadow">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><?= htmlspecialchars($quiz['title']) ?></span>
                    <span class="badge bg-warning text-dark">Attempt <?= $currentAttempts + 1 ?> / <?= $maxAttempts ?></span>
                </div>


                <div class="card-body">
                    <form method="post" action="submit.php">
                        <input type="hidden" name="quiz_id" value="<?= $quizId ?>">
                        <input type="hidden" name="student_id" value="<?= $studentId ?>">

                        <?php
                        $qNum = 1;
                        if ($resultQuestions->num_rows > 0) {
                            while ($row = $resultQuestions->fetch_assoc()) {
                        ?>
                                <div class="question-card">
                                    <p class="question-number">0<?= $qNum ?>) <?= htmlspecialchars($row['question']) ?></p>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answers[<?= $row['Id'] ?>]" value="1" required>
                                        <label class="form-check-label"><?= htmlspecialchars($row['option_1']) ?></label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answers[<?= $row['Id'] ?>]" value="2">
                                        <label class="form-check-label"><?= htmlspecialchars($row['option_2']) ?></label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answers[<?= $row['Id'] ?>]" value="3">
                                        <label class="form-check-label"><?= htmlspecialchars($row['option_3']) ?></label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answers[<?= $row['Id'] ?>]" value="4">
                                        <label class="form-check-label"><?= htmlspecialchars($row['option_4']) ?></label>
                                    </div>
                                </div>
                        <?php
                                $qNum++;
                            }
                        } else {
                            echo "<div class='alert alert-info'>No questions found for this quiz.</div>";
                        }
                        ?>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success btn-lg px-4">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
    </div>

</body>

</html>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>