<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['STUDENT_ID'])) {
    header('Location: ../login.php');
    exit;
}

$db = dbConn();
$studentId = $_SESSION['STUDENT_ID'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assignmentId = $_POST['assignment_id'];
    $termId = $_POST['term_id'];

    // Get due date for check
    $sqlDue = "SELECT a.due_date FROM assignments a WHERE a.Id = '$assignmentId'";
    $resDue = $db->query($sqlDue);
    $rowDue = $resDue->fetch_assoc();
    $dueDate = $rowDue['due_date'];
    $today = date('Y-m-d');


    $file_name = $_FILES['assignment_file']['name'];
    $file_tmp = $_FILES['assignment_file']['tmp_name'];
    $file_size = $_FILES['assignment_file']['size'];
    $file_error = $_FILES['assignment_file']['error'];

    if (!empty($file_name) && empty($errors)) {
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_types = ['pdf', 'docx', 'doc', 'zip'];

        if (in_array($file_ext, $allowed_types)) {
            if ($file_error === 0) {
                if ($file_size <= 5242880) {
                    $file_newName = uniqid('', true) . '.' . $file_ext;
                    $upload_path = '../../system/uploads/' . $file_newName;

                    if (!is_dir('../../system/uploads')) {
                        mkdir('../../system/uploads', 0777, true);
                    }

                    if (move_uploaded_file($file_tmp, $upload_path)) {
                        // Check for existing record
                        $sql_check = "SELECT * FROM assignment_submissions 
                                      WHERE assignment_id = '$assignmentId' AND student_id = '$studentId' AND term_id = '$termId'";
                        $result = $db->query($sql_check);

                        if ($result->num_rows > 0) {
                            // Update existing record
                            $sql = "UPDATE assignment_submissions 
                                    SET file_name = '$file_newName', submitted_at = NOW() 
                                    WHERE assignment_id = '$assignmentId' AND student_id = '$studentId' AND term_id = '$termId'";
                        } else {
                            // Insert new record
                            $sql = "INSERT INTO assignment_submissions 
                                    (assignment_id, student_id, term_id, file_name, submitted_at) 
                                    VALUES 
                                    ('$assignmentId', '$studentId', '$termId', '$file_newName', NOW())";
                        }

                        $db->query($sql);
                        $_SESSION['success_message'] = " Assignment uploaded successfully!";
                        header("Location:success.php?subject_id=..."); 
                        exit;
                    } else {
                        $errors[] = " Failed to upload the file.";
                    }
                } else {
                    $errors[] = " File is too large. Max size is 5MB.";
                }
            } else {
                $errors[] = " An error occurred during file upload.";
            }
        } else {
            $errors[] = " Invalid file type. Only PDF, DOCX, DOC, or ZIP allowed.";
        }
    } else {
        $errors[] = " Please select a file to upload.";
    }
}
?>

<!-- Error Display -->
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-danger text-white">Upload Failed</div>
        <div class="card-body">
            <?php foreach ($errors as $error) : ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endforeach; ?>
            <a href="javascript:history.back()" class="btn btn-secondary">🔙 Go Back</a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
