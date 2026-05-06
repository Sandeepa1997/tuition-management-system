<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['TEACHER_ID'])) {
    header('Location: ../login.php');
    exit;
}
?>


<?php
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
   echo $sql = "INSERT INTO assignment_titles (class_id, title, term_id)
        VALUES ('$class_id', '$title', '$term')";

    $db->query($sql);
    
    echo '<script>
      Swal.fire({
        position: "center",
        icon: "success",
        title: "Your work has been saved",
        showConfirmButton: false,
        timer: 3500
      }).then(function(){window.location="view.php"});
    </script>';
}

?>

<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">Create Assignments</h3>
    </div>

    <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <div class="card-body">
            <input type="hidden" name="subject_id" value="<?= $subjectId ?>">

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

            <div class="row">
                <div class="col-md-6">
                    <div class="mt-3">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control">
                    </div>
                </div>

             <!--Term-Select-->
                <div class="col-md-6">
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

            
            <div class="text-center mt-3">
                <button type="submit" class="btn btn-primary">Proceed</button>
            </div>
        </div>

    </form>

</div>


<?php
$content = ob_get_clean();
include '../layouts.php';
?>