<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['ID'])) {
    header('Location: ../login.php');
    exit;
}
?>


<?php
$db = dbConn();
$studentId = $_SESSION['STUDENT_ID'];
$examId = $_GET['exam_id'];

$sql = "SELECT e.date,e.duration,s.name AS subject,st.reg_no,u.FirstName,u.LastName,g.name AS Grade,e.start_time,t.term
FROM exams e
JOIN classes c ON e.class_id = c.Id
JOIN grade_levels g ON c.Grade_Level_id = g.id
JOIN terms t ON e.term_id = t.Id 
JOIN subjects s ON c.Subject_id = s.Id
JOIN student_enroll se ON se.class_id = c.Id
JOIN students st ON se.student_id = st.Id
JOIN users u ON st.userid= u.ID
WHERE e.Id =$examId AND st.Id = $studentId ";

$results = $db->query($sql);
$row = $results->fetch_assoc();
require_once '../../qr_gen/qrlib.php';

$qr_path = '../../qr_codes/';
if (!file_exists($qr_path)) {
    mkdir($qr_path, 0755, true);
}

// Data to encode — you can make this more detailed
$qrData = "Exam ID: {$examId}\nStudent: {$row['FirstName']} {$row['LastName']} ({$row['reg_no']})\nGrade: {$row['Grade']}\nSubject: {$row['subject']}\nTerm: {$row['term']}\nDate: {$row['date']}";


// Create unique filename
$filename = $qr_path . 'exam_' . $examId . '_student_' . $studentId . '.png';

// Generate QR code
QRcode::png($qrData, $filename, 'L', 4, 2);

// Create relative path for <img>
$qrImagePath = $qr_path . basename($filename);



?>

<!DOCTYPE html>
<html>

<head>
    <title>Exam Admission</title>
   
</head>

<body class="container mt-5">

    <div class="border rounded p-4 shadow-sm">
        <h2 class="text-center mb-4">🎓 Exam Admission Card</h2>

        <div class="row">
            <div class="col-md-8">
                <p><strong>Name:</strong> <?= $row['FirstName'] . ' ' . $row['LastName'] ?></p>
                <p><strong>Reg No:</strong> <?= $row['reg_no'] ?></p>
                <p><strong>Term:</strong> <?= $row['term'] ?></p>
                <p><strong>Subject:</strong> <?= $row['subject'] ?></p>
                <p><strong>Grade:</strong> <?= $row['Grade'] ?></p>
                <p><strong>Date:</strong> <?= $row['date'] ?></p>
                 <p><strong>Start-Time:</strong> <?= $row['start_time'] ?></p>
                <p><strong>Duration:</strong> <?= $row['duration'] ?></p>
            </div>

            <div class="col-md-4 text-end">
                <img src="<?= $qrImagePath ?>" alt="Exam QR Code" class="img-fluid" style="max-width: 150px;">
            </div>
        </div>

        <div class="text-center mt-4">
            <button onclick="window.print()" class="btn btn-primary">🖨️ Download Admission</button>
        </div>
    </div>

</body>

</html>



<?php
$content = ob_get_clean();
include '../layouts.php';
?>