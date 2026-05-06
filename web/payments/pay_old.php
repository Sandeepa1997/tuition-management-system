<?php
ob_start();
include '../../init.php'; // Assuming this correctly initializes your database connection

if (!isset($_SESSION['PARENT_ID'])) {
    echo "Invalid User";
    exit;
}

$regno = $_SESSION['REGNO'];
$parentid = $_SESSION['PARENT_ID'];
$db = dbConn();

// Get student details
$sql = "SELECT u.FirstName, u.LastName, s.Id as student_id, s.reg_no, g.id as grade_id,g.name as grade_name 
        FROM students s
        JOIN users u ON s.userid = u.id
        JOIN grade_levels g ON s.grade_levels_id = g.id
        WHERE s.reg_no = '$regno'";
$result = $db->query($sql);

if ($result->num_rows == 0) {
    echo "Invalid Registration Number";
    exit;
}

$studentRow = $result->fetch_assoc();
$studentid = $studentRow['student_id'];
$studentRegNo = $studentRow['reg_no'];
$studentName = $studentRow['FirstName'] . ' ' . $studentRow['LastName'];
$gradeId = $studentRow['grade_id'];
$gradeName = $studentRow['grade_name'];

// Initialize variables
$messages = [];
$total_amount_to_pay = 0;
$selected_months_details = [];
$payment_date = date('Y-m-d'); // Default to current date
$remark = '';
$payment_method = ''; // Will be set from $_POST on submission


// --- PHP LOGIC TO PROCESS FINAL PAYMENT (now handles both calculation and submission) ---
extract($_POST);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_payment'])) {

    //var_dump($_POST);
    $messages = array();
    if (empty($pay_month)) {
        $messages['pay_month'] = "Atleast one month should be selected!!!";
    }

    if (empty($payment_date)) {
        $messages['payment_date'] = "Please enter a payment date!!!";
    }

    if (empty($pay_final)) {
        $messages['pay_final'] = "Please select a payment method!!!";
    }

    if (empty($messages)) {


        //var_dump($_POST); // For debugging purposes, can uncomment to see $_POST data

        $selected_months = isset($_POST['pay_month']) ? $_POST['pay_month'] : [];
        $payment_method = $_POST['pay_final'] ?? ''; // Get payment method directly from the dropdown
        $payment_date = $_POST['payment_date'] ?? date('Y-m-d');
        $remark = $_POST['remark'] ?? '';
        // The 'final_total_payment' will come from a hidden input, which is updated by JavaScript
        $final_total_payment = $_POST['total_payment_amount'] ?? 0; // Use the value from the totalPaymentSelected input, which JS updates

        if (empty($selected_months)) {
            $messages['selection'] = "Please select at least one month to pay for.";
        } else {
            // Recalculate total and build selected_months_details on the server side for security/validation
            // This is important because the total_payment_amount from JS can be manipulated.
            // Re-calculating here ensures the amounts are correct based on stored class fees.
            foreach ($selected_months as $class_id => $months) {
                foreach ($months as $month_year_key => $fee_value_from_checkbox) { // $fee_value_from_checkbox is the fee from the checkbox value
                    // Fetch class fee from DB for calculation (best practice for security)
                    $fee_sql = "SELECT class_fee, s.name as subject_name FROM classes c JOIN subjects s ON c.Subject_id = s.id WHERE c.Id = " . (int)$class_id;
                    $fee_result = $db->query($fee_sql);
                    if ($fee_row = $fee_result->fetch_assoc()) {
                        $fee = $fee_row['class_fee'];
                        $total_amount_to_pay += $fee; // Accumulate the total based on DB fees
                        $selected_months_details[] = [
                            'class_id' => $class_id,
                            'subject_name' => $fee_row['subject_name'],
                            'month_year' => $month_year_key, // Use the YYYY-MM key from checkbox name
                            'amount' => $fee // Use the fee from the database
                        ];
                    }
                }
            }

            // Now, $total_amount_to_pay holds the server-calculated total.
            // We can use $final_total_payment (from JS) for display, but for actual transaction,
            // it's safer to use the server-calculated $total_amount_to_pay.
            // For PayHere hash, use the server-calculated total.

            if ($payment_method == 'bank_transfer') {

                $file_newName = null;
                if (isset($_FILES['slip']) && $_FILES['slip']['error'] === 0) {
                    $file_name = $_FILES['slip']['name'];
                    $file_tmp = $_FILES['slip']['tmp_name'];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'];

                    if (in_array($file_ext, $allowed_types) && $_FILES['slip']['size'] <= 2097152) {
                        $file_newName = uniqid('', true) . '.' . $file_ext;
                        $file_location = '../../system/uploads/payslips/' . $file_newName;
                        move_uploaded_file($file_tmp, $file_location);
                    } else {
                        $messages['slip'] = "Invalid file type or size (Max 2MB).";
                    }
                } else {
                    $messages['slip'] = "Please upload the payment slip.";
                }

                if (empty($messages)) {

                    // Use the server-validated details
                    $insert_sql = "INSERT INTO student_payment( student_id, grade_id, amount, date,pay_slip,method,parent_id,remark,status) 
                    VALUES  ('$studentid', '$gradeid_hidden','$total_payment_amount','$payment_date','$file_newName','Bank Transfer','$parent_id','$remark','Pending')";
                    $db->query($insert_sql);
                    $payId = $db->insert_id;
                    foreach ($selected_months_details as $detail) { // Use the server-validated details
                        $insert_sql = "INSERT INTO stu_pay_history(student_pay_id,class_id, month) 
                                   VALUES ('$payId','{$detail['class_id']}','{$detail['month_year']}')";
                        $db->query($insert_sql);
                    }
                    echo '<script>
                      Swal.fire({
                         position: "center",
                         icon: "success",
                         title: "bank transfer...",
                         showConfirmButton: false,
                         timer: 2000
                         }).then(function(){window.location.href= "../parent/dashboard.php";});
                  </script>';
                }
            } elseif ($payment_method == 'Card') {
                $sql_student_info = "SELECT u.FirstName, u.LastName, u.Primary_Contact, u.Address, u.City, u.Email FROM users u JOIN students s ON u.Id = s.userid WHERE s.Id = '$studentid'";
                $student_info = $db->query($sql_student_info)->fetch_assoc();

                $order_id = $studentid . '-' . time();
                // Use the server-validated details
                $insert_sql = "INSERT INTO student_payment( student_id, grade_id, amount, date,method,parent_id,remark) 
                    VALUES  ('$studentid', '$gradeid_hidden','$total_payment_amount','$payment_date','Card','$parent_id','$remark')";
                $db->query($insert_sql);
                $payId = $db->insert_id;

                foreach ($selected_months_details as $detail) {
                    //var_dump($detail);

                    // Use the server-validated details
                    $insert_sql = "INSERT INTO stu_pay_history(student_pay_id,class_id, month) 
                                   VALUES ('$payId','{$detail['class_id']}','{$detail['month_year']}')";
                    $db->query($insert_sql);
                }
                $_SESSION['STU_PAY_ID'] = $payId;
                $merchant_id = '1231096'; // Your Merchant ID
                $merchant_secret = 'Mzg3MDYzMTg1NzM2MDg3MzIxODYyNTc3MTIxMDgyNzQzMzE3Nw=='; // Your Merchant Secret
                $currency = 'LKR';
                // Use the server-calculated total for the hash
                $hash = strtoupper(md5($merchant_id . $payId . number_format($total_amount_to_pay, 2, '.', '') . $currency . strtoupper(md5($merchant_secret))));

                echo '<form id="payhere_form" method="post" action="https://sandbox.payhere.lk/pay/checkout">';
                echo '<input type="hidden" name="merchant_id" value="' . $merchant_id . '">';
                echo '<input type="hidden" name="return_url" value="http://localhost/sciencemore/web/payments/return.php">';
                echo '<input type="hidden" name="cancel_url" value="http://localhost/sciencemore/web/payments/cancel.php">';
                echo '<input type="hidden" name="notify_url" value="http://localhost/sciencemore/web/payments/notify.php">';
                echo '<input type="hidden" name="order_id" value="' . $payId . '">';
                echo '<input type="hidden" name="items" value="Class Fees for ' . htmlspecialchars($studentName) . '">';
                echo '<input type="hidden" name="currency" value="' . $currency . '">';
                echo '<input type="hidden" name="amount" value="' . number_format($total_amount_to_pay, 2, '.', '') . '">'; // Use server-calculated total
                echo '<input type="hidden" name="first_name" value="' . htmlspecialchars($student_info['FirstName']) . '">';
                echo '<input type="hidden" name="last_name" value="' . htmlspecialchars($student_info['LastName']) . '">';
                echo '<input type="hidden" name="email" value="' . htmlspecialchars($student_info['Email']) . '">';
                echo '<input type="hidden" name="phone" value="' . htmlspecialchars($student_info['Primary_Contact']) . '">';
                echo '<input type="hidden" name="address" value="' . htmlspecialchars($student_info['Address']) . '">';
                echo '<input type="hidden" name="city" value="' . htmlspecialchars($student_info['City']) . '">';
                echo '<input type="hidden" name="country" value="Sri Lanka">';
                echo '<input type="hidden" name="hash" value="' . $hash . '">';
                echo '</form>';
                echo '<script>
                      Swal.fire({
                         position: "center",
                         icon: "success",
                         title: "Proceeding to Payment...",
                         showConfirmButton: false,
                         timer: 2000
                         }).then(function(){document.getElementById("payhere_form").submit();});
                  </script>';
                exit();
            }
        }
    }
}
// Get enrolled class details (this part remains largely the same)
 $sql2 = "SELECT c.Id AS class_id, G.name AS Grade, s.name AS subject_name, c.class_fee, se.date AS enroll_date
          FROM student_enroll se
          JOIN classes c ON se.class_id = c.Id
          JOIN grade_levels G ON c.Grade_Level_id = G.id
          JOIN subjects s ON c.Subject_id = s.id
          WHERE se.student_id = $studentid";
$result2 = $db->query($sql2);

$classList = [];
if ($result2->num_rows > 0) {
    while ($row2 = $result2->fetch_assoc()) {
        $classId = $row2['class_id'];
        $classFee = $row2['class_fee'];
        $enrollDate = new DateTime($row2['enroll_date']);
        $currentDate = new DateTime();

        $row2['total_paid'] = 0;
        $row2['total_due'] = 0;
        $row2['payment_history'] = [];

        $startMonth = new DateTime($enrollDate->format('Y-m-01'));
        $period = new DatePeriod($startMonth, DateInterval::createFromDateString('1 month'), $currentDate->modify('+1 day'));

        foreach ($period as $dt) {
            $monthYear = $dt->format('Y-m');
            $monthName = $dt->format('F Y');

          echo  $sql_payment_check = "SELECT amount, status 
                      FROM student_payment sp 
                      INNER JOIN stu_pay_history sh ON sp.Id = sh.student_pay_id
                      WHERE sp.student_id = '$studentid' AND sh.class_id = '$classId' 
                      AND DATE_FORMAT(sh.month, '%Y-%m') = '$monthYear'";
            $result_payment_check = $db->query($sql_payment_check);

            $history_item = [
                'month' => $monthName,
                'month_year_code' => $monthYear,
                'amount' => $classFee,
                'status' => 'Due', // default if no record found
                'checkbox_name' => 'pay_month[' . $classId . '][' . $monthYear . ']',
                'is_selectable' => true
            ];

            if ($result_payment_check->num_rows > 0) {
                $paymentRow = $result_payment_check->fetch_assoc();

                if ($paymentRow['status'] == 'Paid') {
                    $row2['total_paid'] += $paymentRow['amount'];
                    $history_item['status'] = 'Paid';
                    $history_item['is_selectable'] = false;
                } elseif ($paymentRow['status'] == 'Pending') {
                    $row2['total_due'] += $classFee;
                    $history_item['status'] = 'Pending';
                    // Decide whether to keep it selectable or not
                    // $history_item['is_selectable'] = false;
                }
            } else {
                $row2['total_due'] += $classFee;
                // 'status' remains 'Unpaid'
            }

            $row2['payment_history'][] = $history_item;
        }
        $classList[] = $row2;
    }
}
?>


<style>
    body {
        background-color: #f8f9fa;
    }

    .container {
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        margin-top: 30px;
        margin-bottom: 30px;
    }

    .form-section {
        padding: 20px;
        background-color: #e9ecef;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .table thead th {
        vertical-align: middle;
    }

    .payment-history-row td {
        padding: 0;
        border: none;
    }

    .payment-history-row .card {
        border: 1px solid #dee2e6;
        background-color: #f1f3f5;
    }
</style>


<div class="container">
    <h3 class="mb-4 text-center">Class Payment</h3>

    <div class="form-section">
        <div class="mb-3 row">
            <label for="studentRegNumber" class="col-sm-3 col-form-label fw-bold">Student Reg Number</label>
            <div class="col-sm-9">
                <input type="text" readonly class="form-control-plaintext" id="studentRegNumber" value="<?= htmlspecialchars($studentRegNo) ?>">
            </div>
        </div>
        <div class="mb-3 row">
            <label for="studentName" class="col-sm-3 col-form-label fw-bold">Student Name</label>
            <div class="col-sm-9">
                <input type="text" readonly class="form-control-plaintext" id="studentName" value="<?= htmlspecialchars($studentName) ?>">
            </div>
        </div>
        <div class="mb-3 row">
            <label for="grade" class="col-sm-3 col-form-label fw-bold">Grade</label>
            <div class="col-sm-9">
                <input type="text" readonly class="form-control-plaintext" id="grade" value="<?= htmlspecialchars($gradeName) ?>">
            </div>
        </div>
    </div>

    <form id="paymentForm" method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="parent_id" value="<?= htmlspecialchars($parentid) ?>">
        <input type="hidden" name="studentid_hidden" value="<?= htmlspecialchars($studentid) ?>">
        <input type="hidden" name="gradeid_hidden" value="<?= htmlspecialchars($gradeId) ?>">

        <h4 class="mb-3">Subject Payment Details</h4>
        <?php if (empty($classList)) : ?>
            <div class="alert alert-info text-center">No classes enrolled for this student.</div>
        <?php else : ?>
            <?php foreach ($classList as $class) : ?>
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Subject: <?= htmlspecialchars($class['subject_name']) ?> (Class Fee: LKR <?= number_format($class['class_fee'], 2) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Subject ID:</strong> <?= htmlspecialchars($class['class_id']) ?><br>
                                <strong>Subject Name:</strong> <?= htmlspecialchars($class['subject_name']) ?><br>
                                <strong>Subject Amount:</strong> LKR <?= number_format($class['class_fee'], 2) ?>
                            </div>
                            <div class="col-md-6">
                                <h6> Payment Details:</h6>
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Payment Month</th>
                                            <th>Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($class['payment_history'] as $history) : ?>
                                            <tr>
                                                <td><?= htmlspecialchars($history['month']) ?></td>
                                                <td class="<?= ($history['status'] == 'Due' || $history['status'] == 'Pending') ? 'text-danger' : 'text-success' ?>">
                                                    <?= htmlspecialchars($history['status']) ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($history['is_selectable']) : ?>
                                                        <div class="form-check">
                                                            <input class="form-check-input selected-month-checkbox" type="checkbox"
                                                                name="<?= htmlspecialchars($history['checkbox_name']) ?>"
                                                                value="<?= htmlspecialchars($history['amount']) ?>"
                                                                data-class-id="<?= htmlspecialchars($class['class_id']) ?>"
                                                                data-month-year="<?= htmlspecialchars($history['month_year_code']) ?>">

                                                            <label class="form-check-label visually-hidden">Pay</label>
                                                        </div>
                                                    <?php else : ?>
                                                        <i class="bi bi-check-circle-fill text-success" title="Paid/Pending"></i>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>

                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <span class="text-danger"><?= @$messages['pay_month'] ?></span>
        <?php endif; ?>

        <hr>

        <h4 class="mb-3">Payment Details</h4>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="totalPaymentSelected" class="form-label fw-bold">Total Payment Selected</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">LKR.</span>
                    <input type="text" class="form-control" id="totalPaymentSelected" name="total_payment_amount" readonly value="0.00">
                </div>
            </div>
            <div class="col-md-6">
                <label for="paymentDate" class="form-label fw-bold">Date</label>
                <input type="date" class="form-control" id="paymentDate" name="payment_date" value="<?= htmlspecialchars($payment_date) ?>" required>
                <span class="text-danger"><?= @$messages['payment_date'] ?></span>
            </div>
            <div class="col-12">
                <label for="remark" class="form-label fw-bold">Remark</label>
                <input type="text" class="form-control" id="remark" name="remark" placeholder="Enter any remarks" value="<?= htmlspecialchars($remark) ?>">

            </div>
            <div class="col-12">
                <label for="payMethod" class="form-label fw-bold">Pay Method</label>
                <select class="form-select" id="payMethod" name="pay_final" required>
                    <option value="">-- Select --</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="Card">Card Payment</option>
                </select>
                <span class="text-danger"><?= @$messages['pay_final'] ?></span>

                <?php if (isset($messages['selection'])) : ?>
                    <div class="alert alert-danger mt-2"><?= $messages['selection'] ?></div>
                <?php endif; ?>
            </div>

            <div id="payment-method-dynamic-sections"></div>


            <div class="col-12 mt-4 text-end">
                <button class="btn btn-success" type="submit" name="confirm_payment" id="confirmPayBtn">Confirm & Pay</button>
                <a href="payment_interface.php" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>


<script>
    $(document).ready(function() {
        // Function to calculate total payment
        function calculateTotal() {
            let total = 0;
            $('.selected-month-checkbox:checked').each(function() {
                const amount = parseFloat($(this).val());
                if (!isNaN(amount)) {
                    total += amount;
                }
            });
            $('#totalPaymentSelected').val(total.toFixed(2));
        }

        // Attach event listener to all checkboxes
        $(document).on('change', '.selected-month-checkbox', calculateTotal);

        // Initial calculation on page load
        calculateTotal();

        // Handle the "Confirm & Pay" button submission
        /* $('#paymentForm').on('submit', function(e) {
             const totalAmount = parseFloat($('#totalPaymentSelected').val());
             const selectedMonthsCount = $('.selected-month-checkbox:checked').length;
             const paymentMethod = $('#payMethod').val();
             let isValid = true;

             // Basic validation
             if (selectedMonthsCount === 0) {
                 Swal.fire('Error', 'Please select at least one month to pay for.', 'error');
                 isValid = false;
             }

             if (!paymentMethod) {
                  Swal.fire('Error', 'Please select a payment method.', 'error');
                  isValid = false;
             }

             if (paymentMethod === 'bank_transfer') {
                 if ($('#slip').length && $('#slip')[0].files.length === 0) {
                     Swal.fire('Error', 'Please upload the payment slip for bank transfer.', 'error');
                     isValid = false;
                 }
             }

             if (!isValid) {
                 e.preventDefault(); // Prevent form submission if validation fails
             } else {
                 // For card payments, show SweetAlert before redirection
                 if (paymentMethod === 'Card') {
                     e.preventDefault(); // Prevent default submission for now
                     Swal.fire({
                         position: "center",
                         icon: "success",
                         title: "Proceeding to Payment...",
                         showConfirmButton: false,
                         timer: 2000
                     }).then(function(){
                         // After SweetAlert, manually submit the form
                         $('#paymentForm')[0].submit();
                     });
                 }
                 // For bank transfer, the form submits normally, and PHP will handle it.
             }
         });*/

        // Toggle payment method sections dynamically
        $('#payMethod').on('change', function() {
            const selectedMethod = $(this).val();
            // Clear previous dynamic sections
            $('#payment-method-dynamic-sections').empty();

            if (selectedMethod === 'bank_transfer') {
                const bankTransferHtml = `
                    <div class="col-12" id="bank-transfer-upload-section">
                        <div class="card p-3 my-2 bg-light">
                            <h5 class="text-success fw-bold"><i class="bi bi-bank"></i> Bank Transfer Details</h5>
                            <p><strong>Account:</strong> 8279000184 | Commercial Bank - Katana | Sciencemore Institute</p>
                            <div class="mb-3">
                                <label for="slip" class="form-label fw-bold">Upload Payment Slip <span class="text-danger">*</span></label>
                                <input type="file" name="slip" id="slip" class="form-control" required>
                                <span class="text-danger"></span>
                            </div>
                        </div>
                    </div>
                `;
                $('#payment-method-dynamic-sections').append(bankTransferHtml);
            } else if (selectedMethod === 'Card') {
                const cardPaymentHtml = `
                    <div class="col-12" id="card-payment-info-section">
                        <div class="card p-3 my-2 bg-light">
                            <p>You will be redirected to the PayHere secure payment gateway to complete your card payment.</p>
                        </div>
                    </div>
                `;
                $('#payment-method-dynamic-sections').append(cardPaymentHtml);
            }
        });
    });
</script>


<?php
$content = ob_get_clean();
include '../layouts.php';
?>