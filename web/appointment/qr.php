<?php
// Define the path where QR codes will be saved
$qr_path = '../../qr_codes/';

// Include the QR library
include '../../qr_gen/qrlib.php';

// Ensure the directory exists
if (!file_exists($qr_path)) {
    mkdir($qr_path, 0755, true);
}

// Set QR parameters
$errorCorrectionLevel = 'L'; // L = Low, M = Medium, Q = Quartile, H = High
$matrixPointSize = 4;        // Size: 1 to 10

// Data to encode in QR
$data = '20259478'; // You can also make this dynamic, like a student ID or appointment ref

// Generate a unique file name
$filename = $qr_path . 'qr_' . md5($data . '|' . $errorCorrectionLevel . '|' . $matrixPointSize) . '.png';

// Generate the QR code only if it doesn't already exist
if (!file_exists($filename)) {
    QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
}

// Output the QR image
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QR Code Display</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow text-center">
            <div class="card-header bg-dark text-white">
                <h4>Your QR Code</h4>
            </div>
            <div class="card-body">
                <p>This QR code contains the data: <strong><?= htmlspecialchars($data) ?></strong></p>
                <img src="<?= $qr_path . basename($filename) ?>" alt="QR Code" class="img-fluid" style="max-width:200px;">
            </div>
        </div>
    </div>
</body>
</html>
