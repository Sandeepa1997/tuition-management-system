<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['TEACHER_ID'])) {
    header('Location: ../login.php');
    exit;
}

$db = dbConn();
$teacherId = $_SESSION['TEACHER_ID'];

// Get subject ID
$sqlSubject = "SELECT s.id AS subject_id FROM teachers t
               JOIN subjects s ON t.subject_id = s.id
               WHERE t.Id = '$teacherId' LIMIT 1";
$resultSubject = $db->query($sqlSubject);
$rowSubject = $resultSubject->fetch_assoc();
$subjectId = $rowSubject['subject_id'] ?? null;

// Get class list
$sqlClasses = "SELECT c.Id AS class_id, g.name AS grade_name, s.name AS subject_name
               FROM classes c
               JOIN grade_levels g ON c.Grade_Level_id = g.id
               JOIN subjects s ON c.Subject_id = s.id
               WHERE c.Teacher_id = '$teacherId'";
$resultClasses = $db->query($sqlClasses);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    extract($_POST);
    $db = dbConn();
    $sql = "INSERT INTO quizzes (class_id, title,type,term_id, attempts)
            VALUES ('$class_id', '$title', '$type', '$term', '$number')";
    $db->query($sql);
}
?>

<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">Create Quiz</h3>
    </div>

    <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <div class="card-body">
            <input type="hidden" name="subject_id" value="<?= $subjectId ?>">

            <!--Get-Class-->
            <div class="mb-3">
                <label>Select Class</label>
                <select name="class_id" class="form-control">
                    <option value="">-- Select Class --</option>
                    <?php while ($row = $resultClasses->fetch_assoc()) { ?>
                        <option value="<?= $row['class_id'] ?>">
                            <?= $row['grade_name'] ?> - <?= $row['subject_name'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <!--Title-->
            <div class="mb-3">
                <label>Title</label>
                <input type="text" name="title" class="form-control">
            </div>

            <!--quiz-type-->
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label mt-3">Select a Type</label>
                    <select name="type" class="form-control" id="type">
                        <option value="">Select Quiz Type</option>
                        <option value="Class Test">Class Test</option>
                        <option value="Practice Quiz">Practice Quiz</option>
                        
                    </select>
                </div>

                <!--Term-Select-->
                <div class="col-md-3">
                    <div class="mt-3">
                        <label for="term" class="form-label">Term</label>
                        <select class="form-control" id="term" name="term">
                            <option value="">Select the Term</option>
                            <?php
                            $db = dbConn();
                            $sql = "SELECT * FROM terms";
                            $result = $db->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                            ?>
                                    <option value="<?= $row['Id'] ?>" <?= $row['Id'] == @$term ? 'selected' : '' ?>>
                                        <?= $row['term'] ?>
                                    </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <!--Number of Attempts-->
                    <div class="mt-3 mx-3">
                        <label>Number of Attempts</label>
                        <input type="number" name="number" class="form-control">
                    </div>
                </div>

                <div class="col-md-3">
                    <!--Submit Button Properly Inside the Form-->
                    <div class="text-center mt-5">
                        <button type="submit" class="btn btn-primary">Proceed</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
