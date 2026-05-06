<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['ID'])) {
    header("Location:../login.php");
    exit;
}
?>

<?php
$db = dbConn();

$class_id = $_GET['class_id'];
$date = date('Y-m-d');



// Get student list
$sql_students = "SELECT st.Id, st.reg_no, u.FirstName, u.LastName
                 FROM student_enroll se
                 JOIN students st ON se.student_id = st.Id
                 JOIN users u ON st.userid = u.Id
                 WHERE se.class_id = $class_id";

$result = $db->query($sql_students);
?>

<div class="container mt-4">
    <div class="card shadow-sm bg-info">
        <h3 class="text-center mb-2 mt-2">Mark Attendance</h3>
    </div>

    <?php if ($result->num_rows > 0) { ?>
        <form action="submit_attendance.php" method="post">
            <input type="hidden" name="class_id" value="<?= $class_id ?>">
            <input type="hidden" name="date" value="<?= $date ?>">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Reg No</th>
                        <th>Name</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['reg_no'] ?></td>
                            <td><?= $row['FirstName'] . ' ' . $row['LastName'] ?></td>
                            <td>
                                <select name="attendance[<?= $row['Id'] ?>]" class="form-control">
                                    <option value="P">Present</option>
                                    <option value="A">Absent</option>
                                </select>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div class="text-center">
                <button type="submit" class="btn btn-success mb-3">Submit</button>
            </div>
        </form>
    <?php } else { ?>
        <div class="alert alert-warning">No students enrolled for this class.</div>
    <?php } ?>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
