<?php
ob_start();
include '../../init.php';

$db = dbConn();
$parentId = $_SESSION['PARENT_ID'];

$sql_1 = "SELECT Id AS student_id 
          FROM students 
          WHERE guardian_id = '$parentId'";
$result_1 = $db->query($sql_1);
?>

<div class="container mt-4">
    <?php
    while ($row_1 = $result_1->fetch_assoc()) {
        $studentId = $row_1['student_id'];

        $sql = "SELECT c.Id AS class_id, c.Class_Name, g.name, se.student_id, c.class_fee, s.reg_no,
                       u.FirstName, u.LastName, c.Grade_Level_id
                FROM student_enroll se 
                INNER JOIN classes c ON se.class_id = c.Id
                INNER JOIN students s ON se.student_id = s.Id
                INNER JOIN users u ON s.Userid = u.Id
                INNER JOIN grade_levels g ON c.Grade_Level_id = g.id 
                WHERE se.student_id = '$studentId'";
        $result = $db->query($sql);

        while ($row = $result->fetch_assoc()) {
    ?>
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <strong><?= $row['FirstName'] ?> <?= $row['LastName'] ?></strong> (Reg No: <?= $row['reg_no'] ?>)
                </div>
                <div class="card-body">
                    <p><strong>Class ID:</strong> <?= $row['class_id'] ?></p>
                    <p><strong>Class Name:</strong> <?= $row['Class_Name'] ?> (<?= $row['name'] ?>)</p>
                    <p><strong>Class Fee:</strong> Rs. <?= number_format($row['class_fee'], 2) ?></p>

                    <?php
                    $classId = $row['class_id'];
                    $gradeId = $row['Grade_Level_id'];

                    $sqlpay = "SELECT * FROM student_payment
                               WHERE student_id='$studentId' AND grade_id='$gradeId' AND class_id='$classId'";
                    $resultpay = $db->query($sqlpay);
                    ?>

                    <div class="table-responsive">
                        <table class="table table-bordered mt-3">
                            <thead class="table-light">
                                <tr>
                                    <th>Year</th>
                                    <th>Month</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($resultpay->num_rows > 0) {
                                    while ($rowpay = $resultpay->fetch_assoc()) {
                                ?>
                                        <tr>
                                            <td><?= $rowpay['pay_year'] ?></td>
                                            <td><?= $rowpay['pay_month'] ?></td>
                                            <td>Rs. <?= number_format($rowpay['amount'], 2) ?></td>
                                            <td>
                                                <?php if ($rowpay['status'] == 'Paid') : ?>
                                                    <span class="badge bg-success"><?= $rowpay['status'] ?></span>
                                                <?php elseif ($rowpay['status'] == 'Pending') : ?>
                                                    <span class="badge bg-warning text-dark"><?= $rowpay['status'] ?></span>
                                                <?php else : ?>
                                                    <span class="badge bg-secondary"><?= $rowpay['status'] ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($rowpay['status'] == 'Pending') { ?>
                                                    <form action="pay.php" method="post" class="d-flex flex-column flex-md-row gap-2 align-items-start">
                                                        <input type="hidden" name="payid" value="<?= $rowpay['Id'] ?>">
                                                        <input type="hidden" name="amount" value="<?= $rowpay['amount'] ?>">
                                                        <input type="hidden" name="parentid" value="<?= $parentId ?>">
                                                        <input type="hidden" name="student_id" value="<?= $studentId ?>">

                                                        <select class="form-select" name="payMethod" required>
                                                            <option value="">-- Select --</option>
                                                            <option value="bank_transfer">Bank Transfer</option>
                                                            <option value="Card">Card Payment</option>
                                                        </select>

                                                        <button class="btn btn-primary" type="submit" name="action" value="classfee">Pay</button>
                                                    </form>
                                                <?php } else {
                                                    echo '-';
                                                } ?>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="text-center">No payment records found.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    <?php
        }
    }
    ?>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
