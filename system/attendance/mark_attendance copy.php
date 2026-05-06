<?php
ob_start();
include '../../init.php';

//confirm whether login to the system
if (!isset($_SESSION['ID'])) {
    header("Location:../login.php");
}
?>

<?php
$db = dbConn();
$sql = "SELECT c.Id,g.name as grade,s.name AS subject
FROM classes c
JOIN grade_levels g ON c.Grade_Level_id = g.Id
JOIN subjects s ON c.Subject_id=s.Id";

$result = $db->query($sql);
?>
<div class="container mt-4">
    <div class="card shadow-sm bg-info">
        <h3 class="text-center mb-2 mt-2">Mark Attendance</h3>
    </div>
    <form method="post" action="">
        <div class="mb-3">
            <label for="class" class="form-label">Select a Class</label>
            <select name="class_id" id="class_id" class="form-control">
                <option value="">Select Class</option>
                <?php
                while ($row = $result->fetch_assoc()) { ?>
                    <option value="<?= $row['Id'] ?>"><?= $row['grade'] ?>-<?= $row['subject'] ?></option>
                <?php
                } ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Select Date</label>
            <input type="date" name="date" id="date" class="form-control">
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary">Proceed</button>
        </div>
    </form>
</div>

<?php
extract($_POST);
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($class_id) && !empty($date)) {

    $classId = $class_id;
    $att_date = $date;

    $sql_students = "SELECT st.Id,st.reg_no,u.FirstName,u.LastName
    FROM student_enroll se
    JOIN students st ON se.student_id = st.Id
    JOIN users u ON st.userid = u.Id
    WHERE se.class_id = $classId";
    $stu_results = $db->query($sql_students);

    if ($stu_results && $stu_results->num_rows > 0) {
?>
        <div class="container mt-4">
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
                        <?php
                        while ($stu_row = $stu_results->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $stu_row['reg_no'] ?></td>
                                <td><?= $stu_row['FirstName'] ?> <?= $stu_row['LastName'] ?></td>
                                <td>
                                    <select name="attendance[<?= $stu_row['Id'] ?>]" class="form-control">
                                        <option value="P">Present</option>
                                        <option value="A">Absent</option>
                                    </select>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>

                <div class="text-center">
                    <button type="submit" class="btn btn-success mb-3">Submit</button>
                </div>
            </form>
        </div>
<?php
    } else {
        echo "<div class='container mt-4'><div class='alert alert-warning'>No students found for the selected class.</div></div>";
    }
}
?>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
