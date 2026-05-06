<?php
ob_start();
include '../../init.php';

$class_id = $_POST['class_id'];
$db = dbConn();
?>

<div class="container mt-5">
    <?php
    if (!isset($_SESSION['ID'])) {
        ?>
        <div class="alert alert-info">Please log in to enroll in a class.</div>
        <a href="../login.php" class="btn btn-primary">Login</a>
        <?php
    } elseif ($_SESSION['USERROLENAME'] !== 'Student') {
        ?>
        <div class="alert alert-danger">Only students can enroll in classes.</div>
        <?php
    } else {
        $Userid = $_SESSION['ID'];
        $sql = "SELECT Id FROM students WHERE Userid = '$Userid'";
        $result = $db->query($sql);

        if ($result->num_rows == 0) {
            ?>
            <div class="alert alert-warning">You are logged in, but not registered as a student. Please register first.</div>
            <a href="../student/register.php" class="btn btn-primary">Register</a>
            <?php
        } else {
            $row = $result->fetch_assoc();
            $studentid = $row['Id'];

            $sql = "SELECT * FROM student_enroll WHERE student_id = '$studentid' AND class_id = '$class_id'";
            $result = $db->query($sql);

            if ($result->num_rows > 0) {
                ?>
                <div class="alert alert-warning">You are already enrolled in this class.</div>
                <?php
            } else {
                $date = date('Y-m-d');
                $sql = "INSERT INTO student_enroll (student_id, class_id, date) 
                        VALUES ('$studentid', '$class_id', '$date')";
                $db->query($sql);
                ?>
                <div class="alert alert-success">You have successfully enrolled in the class.</div>
                <?php
            }
        }
    }
    ?>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
