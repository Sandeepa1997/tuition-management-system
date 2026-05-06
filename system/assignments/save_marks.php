<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['TEACHER_ID'])) {
    header("Location: ../login.php");
    exit;
}

$db = dbConn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST); // This extracts: $submission_ids, $marks, $term_id, $student_ids

    for ($i = 0; $i < count($submission_ids); $i++) {
        $submissionId = $submission_ids[$i];
        $studentId = $student_ids[$i];
        $mark = trim($marks[$i]);

        // Skip if mark is empty
        if ($mark === '' || strtolower($mark) === 'null') {
            continue;
        }

        // Check if a mark already exists for this submission
        $checkSql = "SELECT id FROM assignments_marks WHERE submission_id = '$submissionId'";
        $result = $db->query($checkSql);

        if ($result->num_rows > 0) {
            // Update existing record
            $updateSql = "UPDATE assignments_marks 
                          SET marks = '$mark', term_id = '$term_id', student_id = '$studentId'
                          WHERE submission_id = '$submissionId'";
            $db->query($updateSql);
        } else {
            // Insert new record
            $insertSql = "INSERT INTO assignments_marks 
                          (submission_id, student_id, marks, term_id, created_at) 
                          VALUES ('$submissionId', '$studentId', '$mark', '$term_id', NOW())";
            $db->query($insertSql);
        }
    }
header('Location:view_submission.php');
 
}
?>
