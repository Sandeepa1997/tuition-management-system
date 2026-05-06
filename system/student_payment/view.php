<?php
ob_start();
include '../../init.php';

// Confirm login
if (!isset($_SESSION['ID'])) {
    header("Location:../login.php");
    exit;
}

$db = dbConn();

// Store selected filters before form reload
extract($_POST);
$selectedClass = $_POST['class_id'] ?? '';
$selectedMonth = $_POST['month'] ?? '';

// Fetch class list for dropdown
$sqlClass = "SELECT Id, Class_Name FROM classes";
$resultClass = $db->query($sqlClass);
?>

<form method="post">
    <div class="row">
        <!-- Class Dropdown -->
        <div class="col-md-6">
            <label for="class_id" class="form-label">Class</label>
            <select class="form-control" name="class_id" id="class_id">
                <option value="">Select the Class</option>
                <?php while ($rowClass = $resultClass->fetch_assoc()) { ?>
                    <option value="<?= $rowClass['Id'] ?>" <?= ($selectedClass == $rowClass['Id']) ? 'selected' : '' ?>>
                        <?= $rowClass['Class_Name'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <!-- Month Dropdown -->
        <div class="col-md-6">
            <label for="year" class="form-label">Year</label>
            <select class="form-control" name="year" id="year">
                <option value="">Select the Year</option>
                <?php
                for ($y = 2025; $y <= 2030; $y++) {

                    echo "<option value='$y' " . ($year == $y ? 'selected' : '') . ">$y</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-6">
            <label for="month" class="form-label">Month</label>
            <select class="form-control" name="month" id="month">
                <option value="">Select the Month</option>
                <?php
                for ($m = 1; $m <= 12; $m++) {
                    $monthVal = $m;
                    $monthName = date('F', mktime(0, 0, 0, $m, 10));
                    echo "<option value='$monthVal' " . ($selectedMonth == $monthVal ? 'selected' : '') . ">$monthName</option>";
                }
                ?>
            </select>
        </div>
    </div>
    <button type="submit" class="btn btn-success my-3 mx-2">Search</button>
</form>

<?php
if (!empty($selectedClass) && !empty($selectedMonth)) {
    // Get current date for comparison
    $currentMonth = date('Y-m');

    $sql = "SELECT s.reg_no, u.FirstName, u.LastName, sp.method, sp.status, sp.pay_slip, sp.Id as pay_id FROM student_payment sp 
  INNER JOIN students s ON sp.student_id = s.Id INNER JOIN users u ON s.Userid = u.Id 
  WHERE sp.class_id = '$selectedClass' AND sp.pay_year='$year' AND sp.pay_month='$month'";

    $result = $db->query($sql);
?>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>Reg No</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Slip</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $status = $row['status'];
                        $pay_id = $row['pay_id'];
                        $method = $row['method'];
                        $showPaymentData = ($selectedMonth <= $currentMonth);
                ?>
                        <tr>
                            <td><?= $row['reg_no'] ?></td>
                            <td><?= $row['FirstName'] ?></td>
                            <td><?= $row['LastName'] ?></td>
                            <td><?= $showPaymentData ? ($row['method'] ?? 'N/A') : 'N/A' ?></td>
                            <td>
                                <?php
                                if (isset($row['method'])) {

                                    $status = $row['status'] ?? ''; ?>
                                    <form action="update_status.php" method="post">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="hidden" name="pay_id" value="<?= $pay_id ?>">
                                                <select class="form-control" name="status" id="status">
                                                    <option value="">--</option>
                                                    <option value="Paid" <?= ($status == 'Paid') ? 'selected' : '' ?>>Paid</option>

                                                    <?php
                                                    if ($row['method'] == 'bank_transfer') { ?>
                                                        <option value="Unpaid" <?= ($status == 'Unpaid') ? 'selected' : '' ?>>Unpaid</option>
                                                        <option value="Pending" <?= ($status == 'Pending') ? 'selected' : '' ?>>Pending</option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="submit" class="btn btn-warning w-100">Update Status</button>
                                            </div>
                                        </div>

                                    </form>
                                <?php
                                }
                                ?>

                            </td>
                            <td>
                                <?php
                                if ($row['method'] == 'bank_transfer') {
                                    $slipPath = "../uploads/payslips/" . $row['pay_slip'];

                                ?>
                                    <a href="<?= $slipPath ?>" class="btn btn-success" target="_blank">View Pay Slip</a>

                            </td>

                        </tr>
            <?php }
                            }
                        }
            ?>
            </tbody>
        </table>
    </div>
<?php } ?>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>