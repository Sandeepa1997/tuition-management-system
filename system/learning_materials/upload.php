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

    //File upload
    $file_name = $_FILES['material']['name'];
    $file_tmp = $_FILES['material']['tmp_name'];
    $file_size = $_FILES['material']['size'];
    $file_error = $_FILES['material']['error'];


    if (!empty($file_name)) {
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        //allowed file types
        $file_types = ['jpg', 'jpeg', 'png', 'gif', 'avif', 'pdf'];

        if (in_array($file_ext, $file_types)) {

            if ($file_error === 0) {

                if ($file_size <= 2097152) {

                    $file_newName = uniqid('', true) . '.' . $file_ext;
                    $file_location = '../uploads/' . $file_newName;
                    move_uploaded_file($file_tmp, $file_location);
                } else {
                    $messages['material'] = "The file is too large.Maximum size is 2MB..!";
                }
            } else {
                $messages['material'] = "unknown error occured..!";
            }
        } else {
            $messages['material'] = "File type is not allowed..!";
        }
    } else {
        $messages['material'] = "Please attach a File...!";
    }



    // Insert quiz learning_materials
    $sql_Insert = "INSERT INTO learning_materials (title_id,description,material,status)
                       VALUES ('$title_id','$description','$file_newName','$status')";
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
        <h3 class="card-title">Upload New Materials</h3>
    </div>

    <form method="post" action="" enctype="multipart/form-data">
        <div class="card-body">

            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control" required></textarea>
            </div>


            <div class="mb-3">
                <label for="material" class="form-label">Attach the File</label>
                <input class="form-control" type="file" id="material" name="material">
                <span class="text-danger"><?= @$messages['material'] ?></span>
            </div>



            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="">--</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive </option>
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