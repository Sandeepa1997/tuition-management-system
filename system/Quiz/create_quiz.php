<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['TEACHER_ID'])) {
    header('Location: ../login.php');
    exit;
}
?>

<?php
$teacherId = $_SESSION['TEACHER_ID'];
$db = dbConn();
$quiz_id = $_GET['quiz_id'];

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    extract($_POST);


    // Insert quiz question
    $sql_Insert = "INSERT INTO quiz_questions (question, option_1, option_2, option_3, option_4, correct_answer, quiz_id)
                       VALUES ('$question', '$option_1', '$option_2','$option_3','$option_4','$answer','$quiz_id')";
    $db->query($sql_Insert);

}

?>

<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">Create New Quiz</h3>
    </div>

    <form method="post" action="">
        <div class="card-body">
                    
            <div class="mb-3">
                <label>Question</label>
                <textarea name="question" class="form-control" required></textarea>
            </div>

            <div class="mb-3">
                <label>Option 1</label>
                <input type="text" name="option_1" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Option 2</label>
                <input type="text" name="option_2" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Option 3</label>
                <input type="text" name="option_3" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Option 4</label>
                <input type="text" name="option_4" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Correct Answer</label>
                <select name="answer" class="form-control" required>
                    <option value="">-- Select Correct Option --</option>
                    <option value="1">Option 1</option>
                    <option value="2">Option 2</option>
                    <option value="3">Option 3</option>
                    <option value="4">Option 4</option>
                </select>
            </div>

            <div class="text-center mb-3">
                <button type="submit" class="btn btn-primary">Save Quiz</button>
            </div>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>