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
$title_id = $_GET['title_id'];

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    extract($_POST);

    
    

    // Insert quiz learning_materials
    $sql_Insert = "INSERT INTO assignments (title_id,description,due_date,created_at)
                       VALUES ('$title_id','$description','$duedate','$created_date')";
    $db->query($sql_Insert);
    echo '<script>
          Swal.fire({
    position: "center",
    icon: "success",
    title: "Your work has been saved",
    showConfirmButton: false,
    timer: 3500
    })
    </script>';
}

?>

<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">Upload the Assignments</h3>
    </div>

    <form method="post" action="" enctype="multipart/form-data">
        <div class="card-body">

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" class="form-control" required></textarea>
            </div>




            <div class="mb-3">
                <label for="duedate" class="form-label">Due Date</label>
                   <input type="date" class="form-control" name="duedate" id="duedate">
                </select>

            </div>


         <div class="mb-3">
                <label for="created_date" class="form-label">Created Date</label>
                   <input type="date" class="form-control" name="created_date" id="created_date">
                </select>

            </div>


            <div class="text-center mb-3">
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>