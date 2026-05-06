<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['STUDENT_ID'])) {
    header("Location: ../login.php");
    exit;
}

$db = dbConn();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo "Invalid access method.";
    exit;
}

$studentId = $_POST['student_id'] ?? null;
$quizId = $_POST['quiz_id'] ?? null;
$answers = $_POST['answers'] ?? [];

//  Fetch class_id from the quiz
 $sqlQuiz = "SELECT class_id,term_id FROM quizzes WHERE Id = $quizId";
$resultQuiz = $db->query($sqlQuiz);

if ($resultQuiz->num_rows == 0) {
    echo "Quiz not found.";
    exit;
}
$rowQuiz = $resultQuiz->fetch_assoc();
$classId = $rowQuiz['class_id'];
$term = $rowQuiz['term_id'];

//  Fetch correct answers
$sql = "SELECT Id, correct_answer FROM quiz_questions WHERE quiz_id = $quizId";
$result = $db->query($sql);

$correctAnswers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $correctAnswers[$row['Id']] = $row['correct_answer'];
    }
}

// Calculate score
$totalQuestions = count($correctAnswers);
$correctCount = 0;

foreach ($answers as $questionId => $givenAnswer) {
    $questionId = (int)$questionId;
    if (isset($correctAnswers[$questionId]) && $correctAnswers[$questionId] === $givenAnswer) {
        $correctCount++;
    }
}

$score = $correctCount;
$now = date('Y-m-d H:i:s');

//calculate the percentage
if ($totalQuestions > 0) {
    $percentage = ($score / $totalQuestions) * 100;
} else {
    $percentage = 0;
}


// Insert into quiz_attempts including class_id and num_of_questions
$sqlInsert = "INSERT INTO quiz_attempts (student_id, quiz_id, class_id,term_id,score, attempted_at, num_of_questions,percentage) 
              VALUES ('$studentId', '$quizId', '$classId','$term','$score', '$now', '$totalQuestions','$percentage')";
$db->query($sqlInsert);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quiz Result</title>

</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4>Quiz Result</h4>
            </div>
            <div class="card-body">
                <p>Total Questions: <?= $totalQuestions ?></p>
                <p>Correct Answers: <?= $correctCount ?></p>
                <p>Your Score: <?= $score ?> / <?= $totalQuestions ?></p>
                <p>Percentage: <?= round(($score / $totalQuestions) * 100) ?>%</p>


                <a href="../student/dashboard.php" class="btn btn-success mt-3">Back to Dashboard</a>
            </div>

        </div>
    </div>
</body>

</html>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>