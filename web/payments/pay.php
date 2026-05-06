<?php
ob_start();
include '../../init.php'; // Assuming this correctly initializes your database connection

/*if (!isset($_SESSION['PARENT_ID'])) {
    echo "Invalid User";
    exit;
}*/
$db = dbConn();

extract($_POST);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $sql = "SELECT u.FirstName, u.LastName, s.Id as student_id, s.reg_no, g.id as grade_id, g.name as grade_name,
    u.Email,u.Primary_Contact,u.Address,u.City
        FROM students s
        JOIN users u ON s.userid = u.id
        JOIN grade_levels g ON s.grade_levels_id = g.id
        WHERE s.Id = '$student_id'";
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
    $firstname = $studentRow['FirstName'];
    $lastname =  $studentRow['LastName'];
    $email = $studentRow['Email'];
    $contact =  $studentRow['Primary_Contact'];
    $address = $studentRow['Address'];
    $city = $studentRow['City'];
}

// Get student details


// Initialize variables
$messages = [];
$total_amount_to_pay = 0;
$selected_months_details = [];
$payment_date = date('Y-m-d'); // Default to current date
$remark = '';
$payment_method = ''; // Will be set from $_POST on submission


// --- PHP LOGIC TO PROCESS FINAL PAYMENT (now handles both calculation and submission) ---





if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'upload_slip') {

    if (isset($_FILES['slip'])) {
      echo  $file_name = $_FILES['slip']['name'];
        $file_tmp = $_FILES['slip']['tmp_name'];
        $file_size = $_FILES['slip']['size'];
        $file_error = $_FILES['slip']['error'];

        if (!empty($file_name)) {
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $file_types = ['jpg', 'jpeg', 'png', 'gif', 'avif', 'pdf'];

            if (in_array($file_ext, $file_types)) {
                if ($file_error === 0) {
                    if ($file_size <= 2097152) {
                        $file_newName = uniqid('', true) . '.' . $file_ext;
                        $file_location = '../../system/uploads/payslips/' . $file_newName;
                        move_uploaded_file($file_tmp, $file_location);

                        // Update with file name
                      echo  $sql = "UPDATE student_payment SET pay_slip='$file_newName' WHERE Id='$payment_record_id'";
                        $db->query($sql);

                       /* echo '<script>
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: "Payment success..."
                                }).then(function(){
                                    window.location.href="../parent/dashboard.php"
                                });
                            </script>';*/
                    } else {
                        $messages['slip'] = "The file is too large. Maximum size is 2MB!";
                    }
                } else {
                    $messages['slip'] = "Unknown error occurred!";
                }
            } else {
                $messages['slip'] = "File type is not allowed!";
            }
        } else {
            $messages['slip'] = "Please upload the pay slip!";
        }
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


       


        <h4 class="mb-3">Payment Details</h4>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="totalPaymentSelected" class="form-label fw-bold">Total Payment Selected</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">LKR.</span>
                    <input type="text" class="form-control" id="totalPaymentSelected" name="total_payment_amount" readonly value="<?= $amount ?>">
                </div>
            </div>
            <div class="col-md-6">
                <label for="paymentDate" class="form-label fw-bold">Date</label>
                <input type="date" class="form-control" id="paymentDate" name="payment_date" value="<?= htmlspecialchars($payment_date) ?>" required>
                <span class="text-danger"><?= @$messages['payment_date'] ?></span>
            </div>

        </div>




        <?php
        if ($payMethod == 'bank_transfer') {

        ?>
            <form id="paymentForm" method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="parent_id" value="<?= htmlspecialchars($parentid) ?>">
                <input type="hidden" name="payment_record_id" value="<?= htmlspecialchars($payid) ?>">
                <input type="hidden" name="amount" value="<?= htmlspecialchars($amount) ?>">
                <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id) ?>">
                 <input type="hidden" name="payMethod" value="bank_transfer">

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
                <button type="submit" name="action" value="upload_slip">Upload Slip</button>
            </form>
        <?php
        }

        if($payMethod=='Card'){

        ?>
            <?php
            $merchant_id = '1231096';
            $order_id = $payid;
            $amount = $amount;
            $currency = 'LKR';
            $merchant_secret = 'NTYyODE4NjA1OTQyODcyNjM2NDAzNDc5NTY3NDE0NDEyNTkxMTE=';

            $hash = strtoupper(
                md5(
                    $merchant_id .
                        $order_id .
                        number_format($amount, 2, '.', '') .
                        $currency .
                        strtoupper(md5($merchant_secret))
                )
            );
            ?>
            <form method="post" action="https://sandbox.payhere.lk/pay/checkout">
                <input type="hidden" name="merchant_id" value="1231096">
                <input type="hidden" name="return_url" value="http://localhost/sciencemore/web/payments/return.php">
                <input type="hidden" name="cancel_url" value="http://localhost/sciencemore/web/payments/cancel.php">
                <input type="hidden" name="notify_url" value="http://localhost/sciencemore/web/payments/notify.php">
                <input type="hidden" name="order_id" value="<?= $payid ?>">
                <input type="hidden" name="items" value="classfee">
                <input type="hidden" name="currency" value="LKR">
                <input type="hidden" name="amount" value="<?= $amount ?>">
                <input type="hidden" name="first_name" value="<?= $firstname ?>">
                <input type="hidden" name="last_name" value="<?= $lastname ?>">
                <input type="hidden" name="email" value="<?= $email ?>">
                <input type="hidden" name="phone" value="<?= $contact ?>">
                <input type="hidden" name="address" value="<?= $address ?>">
                <input type="hidden" name="city" value="<?= $city ?>">
                <input type="hidden" name="country" value="Sri Lanka">


                <input type="hidden" name="hash" value="<?= $hash ?>"><br><br>
                
                <button type="submit" class="btn btn-success">Pay Now</button>

            </form>
        <?php
        }
        ?>




        <?php
        $content = ob_get_clean();
        include '../layouts.php';
        ?>