<?php
ob_start();
include '../../init.php';

// Confirm whether login to the system
if (!isset($_SESSION['ID'])) {
    header("Location:../login.php");
    exit;
}
?>
<?php
$db = dbConn();

// Fetch all classes for the dropdown
$class_sql = "SELECT Id, Class_Name FROM classes GROUP BY Id";
$class_result = $db->query($class_sql);

// Handle form submission
$report_data = [];
$selected_class = '';
$selected_month = '';
$class_name = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);
    $selected_class = $class_id;
    $selected_month = $month;

    // Get class name
    $class_info = "SELECT Class_Name FROM classes WHERE Id = '$class_id' LIMIT 1";
    $res_class = $db->query($class_info);
    $row_class = $res_class->fetch_assoc();
    $class_name = $row_class['Class_Name'];

    // Get attendance records for the class and month
     $attendance_sql = "SELECT ca.*, s.reg_no, u.FirstName
FROM class_attendance ca
JOIN students s ON ca.student_id = s.Id
JOIN users u ON s.userid = u.Id
WHERE ca.class_id = ' $class_id' 
  AND MONTH(ca.date) = '$month'
ORDER BY ca.date ASC";


    $attendance_result = $db->query($attendance_sql);
    if ($attendance_result->num_rows > 0) {
        while ($row = $attendance_result->fetch_assoc()) {
            $report_data[] = $row;
        }
    }
}
?>

<h3>Attendance Report</h3>

<form method="post" action="">
    <div class="form-group">
        <label>Select Class</label>
        <select name="class_id" class="form-control" required>
            <option value="">--Select--</option>
            <?php while ($row = $class_result->fetch_assoc()) { ?>
                <option value="<?= $row['Id'] ?>" <?= ($selected_class == $row['Id']) ? 'selected' : '' ?>>
                    <?= $row['Class_Name'] ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label>Select Month</label>
        <select name="month" class="form-control" required>
            <option value="">--Select--</option>
            <?php for ($m = 1; $m <= 12; $m++) {
                $month_name = date('F', mktime(0, 0, 0, $m, 10));
            ?>
                <option value="<?= $m ?>" <?= ($selected_month == $m) ? 'selected' : '' ?>>
                    <?= $month_name ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <button type="submit" class="btn btn-primary mt-2">Load Report</button>
</form>

<?php if (!empty($report_data)) { ?>
    <hr>
    <h5>Class: <?= $class_name ?> | Month: <?= date('F', mktime(0, 0, 0, $selected_month, 10)) ?></h5>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Reg No</th>
                <th>Student Name</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($report_data as $row) { ?>
                <tr>
                    <td><?= $row['reg_no'] ?></td>
                    <td><?= $row['FirstName'] ?></td>
                    <td><?= $row['date'] ?></td>
                    <td><?= $row['status'] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') { ?>
    <p>No attendance records found for this class and month.</p>
<?php } ?>


<?php
$content = ob_get_clean();
include '../layouts.php';
?>