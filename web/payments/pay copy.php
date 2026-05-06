<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['PARENT_ID'])) {
    echo "Invalid User";
    exit;
}

$regno = $_SESSION['REGNO'];
$parentid = $_SESSION['PARENT_ID'];
$db = dbConn();

// Get student details
$sql = "SELECT * FROM students WHERE reg_no ='$regno'";
$result = $db->query($sql);

if ($result->num_rows == 0) {
    echo "Invalid Registration Number";
    exit;
}

$row = $result->fetch_assoc();
$studentid = $row['Id'];

// Link parent and student if not already linked
$check = "SELECT * FROM parent_student WHERE parent_id = $parentid AND student_id = $studentid";
$checkresult = $db->query($check);

if ($checkresult->num_rows == 0) {
    $insert = "INSERT INTO parent_student (parent_id, student_id, reg_no)
               VALUES ('$parentid','$studentid','$regno')";
    $db->query($insert);
}

// Get enrolled class details
$sql2 = "SELECT  c.Id AS id, G.name AS Grade, s.name AS subject_name, c.class_fee,c.month,
u.FirstName,u.LastName
         FROM student_enroll se
         JOIN classes c ON se.class_id = c.Id
         JOIN students st ON se.student_id = st.Id
         JOIN users u ON st.userid = u.Id
         JOIN grade_levels G ON c.Grade_Level_id = G.id
         JOIN subjects s ON c.Subject_id = s.id
         WHERE se.student_id = $studentid";

$result2 = $db->query($sql2);

$classList = [];
if ($result2->num_rows > 0) {
    while ($row2 = $result2->fetch_assoc()) {
        $classList[] = $row2;
    }
}
?>

<?php if (!empty($classList)) {
    $studentName = $classList[0]['FirstName'] . ' ' . $classList[0]['LastName'];
    $gradeName = $classList[0]['Grade'];
?>
    <div class="container">
        <div class="mb-3 mt-4">
            <h5 class="fw-bold">Student Name: <span class="text-primary"><?= $studentName ?></span></h5>
            <h5 class="fw-bold">Grade: <span class="text-success"><?= $gradeName ?></span></h5>
        </div>

        <form method="post" class="mt-3">
            <div class="row">
                <div class="col-md-6 col-lg-6">
                    <label for="selected_class_id" class="form-label fw-bold">Select Subject to Pay For</label>
                    <select name="selected_class_id" id="selected_class_id" class="form-select rounded-pill" required>
                        <option value="">-- Select Subject --</option>
                        <?php foreach ($classList as $class) { ?>
                            <option value="<?= $class['id'] ?>" <?= (isset($_POST['selected_class_id']) && $_POST['selected_class_id'] == $class['id']) ? 'selected' : '' ?>>
                                <?= $class['subject_name'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-6 col-lg-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Load Payment Info</button>
                </div>
            </div>
        </form>

        <h5 class="text-primary fw-bold mt-4">Enrolled Classes</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle text-center">
                <thead class="table-primary">
                    <tr>
                        <th>Name</th>

                        <th>Subject</th>
                        <th>Fee (Rs.)</th>
                        <th>Month</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($classList as $class) { ?>
                        <tr>
                           
                            <td><?= $class['subject_name'] ?></td>
                            <td><?= number_format($class['class_fee'], 2) ?></td>
                            <td><?= $class['month'] ?></td>
                            <td>
                                <!-- Status logic can go here if needed -->
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
<?php } ?>


<!-- Payment Form -->
<form action="" method="post" enctype="multipart/form-data">
    <div class="container my-3">
        <div class="row justify-content-start">
          

       

            <div class="col-md-6 col-lg-6">
                <label for="pay" class="form-label fw-bold">Select Your Payment Method</label>
                <select class="form-select rounded-pill" name="pay" id="pay">
                    <option value="">-- Select Method --</option>
                    <option value="bank_transfer" <?= (isset($_POST['pay']) && $_POST['pay'] == 'bank_transfer') ? 'selected' : '' ?>>
                        Bank Transfer
                    </option>
                    <option value="Card" <?= (isset($_POST['pay']) && $_POST['pay'] == 'Card') ? 'selected' : '' ?>>
                        Card Payment
                    </option>
                </select>

            </div>
            <div class="col-md-6 col-lg-6">
                <label for="date" class="form-label fw-bold">Date</label>
                <input type="date" name="date" id="date" class="form-control rounded-pill" value="<?= isset($_POST['date']) ? $_POST['date'] : date('Y-m-d') ?>">
            </div>


            <div class="container mt-2">
                <button class="btn btn-primary" type="submit">Proceed</button>
            </div>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);
    $messages = array();
    $regno = $_SESSION['REGNO'];

    

    $selectedClass = $classList[$class_index];
    $grade = $selectedClass['Grade'];
    $subject = $selectedClass['subject_name'];
    $month = $selectedClass['month'];
    $classfee = $selectedClass['class_fee'];
    $class_id = $selectedClass['id']; //  Set the class ID
    $file_newName = null; // Prevent undefined variable warning

    // bank transfer
    if ($pay == 'bank_transfer') {
        if (isset($_FILES['slip'])) {
            $file_name = $_FILES['slip']['name'];
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
                            $file_location = '../../system/uploads/' . $file_newName;
                            move_uploaded_file($file_tmp, $file_location);
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


        // Get student name
        $db = dbConn();
        $sql_1 = "SELECT u.FirstName, u.LastName
                  FROM students st
                  JOIN users u ON st.userid = u.Id
                  WHERE st.Id = $studentid";
        $result_1 = $db->query($sql_1);

        if ($result_1->num_rows > 0) {
            $row_1 = $result_1->fetch_assoc();
            $firstname = $row_1['FirstName'];
            $lastname = $row_1['LastName'];
        }

        // Insert only if no validation errors and slip is uploaded
        if (empty($messages) && $file_newName) {
            $method = 'bank_transfer';
            $paySlip = $file_newName;
            $sql_insert = "INSERT INTO student_payment (student_id, class_id, amount, date, pay_slip, method)
                           VALUES ('$studentid', '$class_id', '$classfee', '$date', '$paySlip', '$method')";
            $db->query($sql_insert);
        }

?>
        <div class="container mt-3">
            <div class="border p-4 rounded bg-white mb-4 shadow-sm">
                <h5 class="text-success fw-bold mb-3"><i class="bi bi-bank"></i> Payment Details</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Bank Account Number:</strong><br> 8279000184</p>
                        <p><strong>Bank:</strong><br> Commercial Bank - Katana Branch</p>
                        <p><strong>Account Holder:</strong><br> Sciencemore Institute</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Student Name:</strong><br> <?= $firstname . ' ' . $lastname ?></p>
                        <p><strong>Grade:</strong> <?= $grade ?></p>
                        <p><strong>Subject:</strong> <?= $subject ?></p>
                        <p><strong>Month:</strong> <?= $month ?></p>
                        <p><strong>Class Fee:</strong> Rs. <?= number_format($classfee, 2) ?></p>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="slip" class="form-label fw-bold">Upload Payment Slip <span class="text-danger">*</span></label>
                <input type="file" name="slip" id="slip" class="form-control">
                <span class="text-danger" style="font-size: 13px;"><?= @$messages['slip'] ?></span>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success px-4 py-2">
                    <i class="bi bi-send-fill me-1"></i> Submit
                </button>
            </div>

        </div>
    <?php
    }
    //card
    if ($pay == 'Card') {
        $db = dbConn();
        $sql3 = "SELECT u.FirstName, u.LastName, u.Primary_Contact, u.Address, u.City, u.Email
                 FROM students s 
                 JOIN users u ON s.userid = u.Id
                 WHERE s.Id = '$studentid'";
        $result3 = $db->query($sql3);

        if ($result3->num_rows > 0) {
            $row3 = $result3->fetch_assoc();
            $firstname = $row3['FirstName'];
            $lastname = $row3['LastName'];
            $contact = $row3['Primary_Contact'];
            $address = $row3['Address'];
            $email = $row3['Email'];
            $city = $row3['City'];

            $method = 'Card';
            $paySlip = ''; // no slip for card payment

            $sql_insert = "INSERT INTO student_payment (student_id, class_id, amount, date, pay_slip,method)
               VALUES ('$studentid', '$class_id', '$classfee', '$date', '$paySlip', '$method')";
            $db->query($sql_insert);
        }
    ?>
        <div class="container">
            <div class="container mt-3" style="max-width: 500px;">
                <div class="card shadow-sm p-3 rounded">
                    <h5 class="text-center text-primary mb-3 fw-bold">Class Fee</h5>
                    <p><strong>Student:</strong> <?= $firstname . " " . $lastname ?></p>
                    <p><strong>Grade:</strong> <?= $grade ?></p>
                    <p><strong>Subject:</strong> <?= $subject ?></p>
                    <p><strong>Month:</strong> <?= $month ?></p>
                    <p><strong>Total Fee:</strong> Rs. <?= number_format($classfee, 2) ?></p>
                </div>
            </div>

            <form method="post" action="https://sandbox.payhere.lk/pay/checkout">
                <input type="hidden" name="merchant_id" value="1231096">
                <input type="hidden" name="return_url" value="http://localhost/sciencemore/web/payments/return.php">
                <input type="hidden" name="cancel_url" value="http://localhost/sciencemore/web/payments/cancel.php">
                <input type="hidden" name="notify_url" value="http://localhost/sciencemore/web/payments/notify.php">
                <input type="hidden" name="order_id" value="<?= $studentid ?>">
                <input type="hidden" name="items" value="<?= $grade ?>">
                <input type="hidden" name="currency" value="LKR">
                <input type="hidden" name="amount" value="<?= $classfee ?>">
                <input type="hidden" name="first_name" value="<?= $firstname ?>">
                <input type="hidden" name="last_name" value="<?= $lastname ?>">
                <input type="hidden" name="email" value="<?= $email ?>">
                <input type="hidden" name="phone" value="<?= $contact ?>">
                <input type="hidden" name="address" value="<?= $address ?>">
                <input type="hidden" name="city" value="<?= $city ?>">
                <input type="hidden" name="country" value="Sri Lanka">

                <?php
                $merchant_id = '1231096';
                $order_id = $studentid;
                $amount = $classfee;
                $currency = 'LKR';
                $merchant_secret = 'Mzg3MDYzMTg1NzM2MDg3MzIxODYyNTc3MTIxMDgyNzQzMzE3Nw==';

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
                <input type="hidden" name="hash" value="<?= $hash ?>"><br><br>
                <button type="submit" class="btn btn-success">Pay Now</button>
            </form>
        </div>
<?php
    }
}
?>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>