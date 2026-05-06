<?php
ob_start();
include '../../init.php';

$messages = array();
$regno = $_POST['regno'] ?? null;
$resultPayments = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($regno)) {
        $messages['regno'] = "Please enter a Registration Number!";
    } else {
        $db = dbConn();

        // 1. Get student by reg_no
        $sqlStudent = "SELECT * FROM students WHERE reg_no = '$regno'";
        $resultStudent = $db->query($sqlStudent);

        if ($resultStudent->num_rows == 0) {
            $messages['regno'] = "No student found with this Registration Number!";
        } else {
            $studentRow = $resultStudent->fetch_assoc();
            $studentId = $studentRow['Id'];
            $guardianId = $studentRow['guardian_id'];

            // 2. Get parent user NIC from session
            $loggedUserId = $_SESSION['ID']; // Logged-in user ID
            $sqlNIC = "SELECT NIC_No FROM users WHERE Id = $loggedUserId";
            $resultNIC = $db->query($sqlNIC);

            if ($resultNIC->num_rows == 0) {
                $messages['regno'] = "User details not found!";
            } else {
                $nicNo = $resultNIC->fetch_assoc()['NIC_No'];

                // 3. Get NIC of student's guardian (parent)
                $sqlGuardianNIC = "SELECT u.NIC_No 
                                   FROM parents p
                                   JOIN users u ON p.Userid = u.Id
                                   WHERE p.Id = $guardianId";
                $resultGuardian = $db->query($sqlGuardianNIC);

                if ($resultGuardian->num_rows == 0) {
                    $messages['regno'] = "Guardian data not found!";
                } else {
                    $guardianNIC = $resultGuardian->fetch_assoc()['NIC_No'];

                    if ($nicNo !== $guardianNIC) {
                        $messages['regno'] = "Please enter your own child's registration number!";
                    } else {
                        // 4. Fetch payment records
                        $sqlPayments = "SELECT 
                                          p.Id as payment_id,
                                          p.amount,
                                          p.date as payment_date,
                                          p.pay_slip,
                                          p.method,
                                          p.status,
                                          p.remark,
                                          p.pay_year,
                                          p.pay_month,
                                          u.FirstName,
                                          u.LastName,
                                          c.Class_Name
                                        FROM student_payment p
                                        JOIN students s ON p.student_id = s.Id
                                        JOIN users u ON s.userid = u.Id
                                        JOIN classes c ON p.class_id = c.Id
                                        WHERE p.student_id = '$studentId'
                                        ORDER BY p.date DESC, p.pay_year DESC, p.pay_month DESC";
                        $resultPayments = $db->query($sqlPayments);
                    }
                }
            }
        }
    }
}

// Function to get status badge class
function getStatusBadge($status) {
    switch(strtolower($status)) {
        case 'paid':
        case 'approved':
            return 'bg-success';
        case 'pending':
            return 'bg-warning text-dark';
        case 'rejected':
        case 'failed':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}

// Function to format payment method
function formatPaymentMethod($method) {
    switch(strtolower($method)) {
        case 'cash':
            return '<span class="badge bg-info">Cash</span>';
        case 'bank':
            return '<span class="badge bg-primary">Bank Transfer</span>';
        case 'online':
            return '<span class="badge bg-success">Online Payment</span>';
        case 'card':
            return '<span class="badge bg-warning text-dark">Card</span>';
        default:
            return '<span class="badge bg-secondary">' . ucfirst($method) . '</span>';
    }
}
?>

<!-- HTML Output -->
<div class="container mt-5">
    <h4 class="mb-4">View Child's Payment History</h4>

    <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="p-4 border rounded shadow-sm bg-light mb-4">
        <div class="mb-3">
            <label for="regno" class="form-label fw-bold">Student Registration Number</label>
            <input type="text" name="regno" id="regno" class="form-control" placeholder="e.g., R123456" value="<?= htmlspecialchars($regno) ?>">
            <span class="text-danger" style="font-size: 13px;"><?= @$messages['regno'] ?></span>
        </div>
        <div class="text-end">
            <button type="submit" class="btn btn-primary px-4">View Payments</button>
        </div>
    </form>

    <?php if (isset($resultPayments) && $resultPayments->num_rows > 0): ?>
        <?php 
        $studentInfo = $resultPayments->fetch_assoc();
        mysqli_data_seek($resultPayments, 0); // Reset pointer
        $totalPaid = 0;
        $pendingAmount = 0;
        ?>
        
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Payment History for <?= htmlspecialchars($studentInfo['FirstName'] . ' ' . $studentInfo['LastName']) ?> (<?= htmlspecialchars($regno) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php 
                        // Calculate totals
                        mysqli_data_seek($resultPayments, 0);
                        while ($row = $resultPayments->fetch_assoc()) {
                            if (strtolower($row['status']) == 'paid' || strtolower($row['status']) == 'approved') {
                                $totalPaid += $row['amount'];
                            } elseif (strtolower($row['status']) == 'pending') {
                                $pendingAmount += $row['amount'];
                            }
                        }
                        mysqli_data_seek($resultPayments, 0); // Reset pointer again
                        ?>
                        
                        <div class="row text-center mb-3">
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h6>Total Paid</h6>
                                        <h4>Rs. <?= number_format($totalPaid, 2) ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-warning text-dark">
                                    <div class="card-body">
                                        <h6>Pending</h6>
                                        <h4>Rs. <?= number_format($pendingAmount, 2) ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h6>Total Payments</h6>
                                        <h4><?= $resultPayments->num_rows ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Payment ID</th>
                        <th>Date</th>
                        <th>Month/Year</th>
                        <th>Class</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Pay Slip</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $resultPayments->fetch_assoc()): ?>
                        <tr>
                            <td><span class="badge bg-secondary">#<?= $row['payment_id'] ?></span></td>
                            <td><?= date('d-m-Y', strtotime($row['payment_date'])) ?></td>
                            <td>
                                <strong><?= date('F', mktime(0, 0, 0, $row['pay_month'], 1)) ?> <?= $row['pay_year'] ?></strong>
                            </td>
                            <td><?= htmlspecialchars($row['Class_Name']) ?></td>
                            <td><strong>Rs. <?= number_format($row['amount'], 2) ?></strong></td>
                            <td><?= formatPaymentMethod($row['method']) ?></td>
                            <td>
                                <span class="badge <?= getStatusBadge($row['status']) ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($row['pay_slip'])): ?>
                                    <a href="../../system/uploads/payslips/<?= $row['pay_slip'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">No slip</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($row['remark'])): ?>
                                    <small class="text-muted"><?= htmlspecialchars($row['remark']) ?></small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Payment Summary by Month -->
        <div class="mt-4">
            <h5>Monthly Payment Summary</h5>
            <?php 
            mysqli_data_seek($resultPayments, 0);
            $monthlyPayments = array();
            while ($row = $resultPayments->fetch_assoc()) {
                $monthYear = $row['pay_month'] . '-' . $row['pay_year'];
                if (!isset($monthlyPayments[$monthYear])) {
                    $monthlyPayments[$monthYear] = array(
                        'month' => date('F', mktime(0, 0, 0, $row['pay_month'], 1)),
                        'year' => $row['pay_year'],
                        'total' => 0,
                        'paid' => 0,
                        'pending' => 0,
                        'count' => 0
                    );
                }
                $monthlyPayments[$monthYear]['total'] += $row['amount'];
                $monthlyPayments[$monthYear]['count']++;
                
                if (strtolower($row['status']) == 'paid' || strtolower($row['status']) == 'approved') {
                    $monthlyPayments[$monthYear]['paid'] += $row['amount'];
                } elseif (strtolower($row['status']) == 'pending') {
                    $monthlyPayments[$monthYear]['pending'] += $row['amount'];
                }
            }
            
            if (!empty($monthlyPayments)): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead class="table-secondary">
                            <tr>
                                <th>Month</th>
                                <th>Year</th>
                                <th>Total Amount</th>
                                <th>Paid</th>
                                <th>Pending</th>
                                <th>Payments Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($monthlyPayments as $data): ?>
                                <tr>
                                    <td><?= $data['month'] ?></td>
                                    <td><?= $data['year'] ?></td>
                                    <td>Rs. <?= number_format($data['total'], 2) ?></td>
                                    <td><span class="text-success">Rs. <?= number_format($data['paid'], 2) ?></span></td>
                                    <td><span class="text-warning">Rs. <?= number_format($data['pending'], 2) ?></span></td>
                                    <td><span class="badge bg-info"><?= $data['count'] ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
    <?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($messages)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No payment records found for this student.
        </div>
    <?php endif; ?>
</div>

<style>
.table th {
    font-size: 14px;
    font-weight: 600;
}

.table td {
    font-size: 13px;
    vertical-align: middle;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.badge {
    font-size: 0.75em;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.table-responsive {
    border-radius: 0.375rem;
    overflow: hidden;
}
</style>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>