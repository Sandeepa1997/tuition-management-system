<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['STUDENT_ID'])) {
    echo "Access denied.";
    exit;
}

$studentId = $_SESSION['STUDENT_ID'];
$db = dbConn();

// Get enrolled class IDs
$sql = "SELECT class_id FROM student_enroll WHERE student_id = $studentId";
$result = $db->query($sql);

$classIds = [];
while ($row = $result->fetch_assoc()) {
    $classIds[] = $row['class_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Quizzes</title>
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 1rem;
        }
        .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .quiz-title {
            font-weight: 500;
        }
        .icon {
            color:rgb(13, 23, 37);
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                 
                </div>
                <div class="card-body">

                    <?php
                    if (!empty($classIds)) {
                        $classIdList = implode(',', $classIds);
                        $sqlQuizzes = "SELECT * FROM quizzes WHERE class_id IN ($classIdList)";
                        $resultQuizzes = $db->query($sqlQuizzes);

                        if ($resultQuizzes->num_rows > 0) {
                            echo '<div class="list-group">';
                            while ($quiz = $resultQuizzes->fetch_assoc()) {
                                echo '<a href="attempt_quiz.php?quiz_Id=' . $quiz['Id'] . '" class="list-group-item list-group-item-action">';
                                echo '<span class="quiz-title">' . htmlspecialchars($quiz['title']) . '</span>';
                                echo '<i class="bi bi-arrow-right-circle-fill icon"></i>';
                                echo '</a>';
                            }
                            echo '</div>';
                        } else {
                            echo '<div class="alert alert-info text-center">No quizzes found for this class</div>';
                        }
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
</div>


</body>
</html>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
