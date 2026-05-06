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
            <label for="month" class="form-label">Month</label>
            <select class="form-control" name="month" id="month">
                <option value="">Select the Month</option>
                <?php
                for ($m = 1; $m <= 12; $m++) {
                    $monthVal = date('Y-m', strtotime(date('Y') . "-$m"));
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

    $sql = "SELECT s.reg_no, u.FirstName, u.LastName, sp.method, sp.status, sp.pay_slip, sp.Id as pay_id
        FROM student_enroll se
        INNER JOIN students s ON se.student_id = s.Id
        INNER JOIN users u ON s.Userid = u.Id
        LEFT JOIN student_payment sp ON s.Id = sp.student_id
        LEFT JOIN stu_pay_history sph ON sp.Id = sph.student_pay_id 
            AND sph.month = '$selectedMonth' 
            AND sph.class_id = '$selectedClass'
        WHERE se.class_id = '$selectedClass'
          AND DATE_FORMAT(se.date, '%Y-%m') <= '$selectedMonth'
          AND (sp.Id IS NULL OR sph.student_pay_id IS NOT NULL)";

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
                        // Only show payment data if it's for the current or past months
                        $showPaymentData = ($selectedMonth <= $currentMonth);
                ?>
                        <tr>
                            <td><?= $row['reg_no'] ?></td>
                            <td><?= $row['FirstName'] ?></td>
                            <td><?= $row['LastName'] ?></td>
                            <td><?= $showPaymentData ? ($row['method'] ?? 'N/A') : 'N/A' ?></td>
                            <!--<td> $showPaymentData ? ($row['status'] ?? 'Unpaid') : 'Not Due' ?></td> -->
                            <td>
                               
                               
                                <form action="update_status.php" method="post">
                                    <div class="row">
                                        <div class="col-md-6">
                                    <input type="hidden" name="pay_id" value="<?= $pay_id ?>">
                                    <select class="form-control" name="status" id="status">
                                        <option value="">--</option>
                                        <option value="Paid" <?= ($status == 'Paid') ? 'selected' : '' ?>>Paid
                                        </option>
                                        <option value="Unpaid" <?= ($status == 'Unpaid') ? 'selected' : '' ?>>
                                            Unpaid</option>
                                        <option value="Pending" <?= ($status == 'Pending') ? 'selected' : '' ?>>
                                            Pending</option>

                                    </select>
                                        </div>

                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-warning w-100">
                                            Update Status
                                        </button>
                                    </div>
                                    </div>

                                </form>

                            </td>
                            <td>
                                <?php
                                if ($showPaymentData && $row['method'] == 'Bank Transfer' && !empty($row['pay_slip'])) {
                                    $slipPath = "../uploads/payslips/" . $row['pay_slip'];
                                    if (file_exists($slipPath)) {
                                ?>
                                        <a href="view_payslip.php?payslip=<?= $slipPath ?>" class="btn btn-success" target="_blank">View Pay Slip </a>



                                <?php
                                    } else {
                                        echo 'File not found';
                                    }
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>

                        </tr>
                <?php }
                } else {
                    echo '<tr><td colspan="7">No records found.</td></tr>';
                } ?>
            </tbody>
        </table>


    </div>




<?php } ?>



<?php
$content = ob_get_clean();
include '../layouts.php';
?>