<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['TEACHER_ID'])) {
    header('Location: ../login.php');
    exit;
}
?>

<?php
$db = dbConn();
$teacherId = $_SESSION['TEACHER_ID'];

// Handle marks form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['marks']) && isset($_POST['student_id'])) {
    $marks = $_POST['marks'];
    $studentIds = $_POST['student_id'];
    $examId = $_POST['exam_id'];

    foreach ($marks as $index => $mark) {
        $studentId = (int) $studentIds[$index];
        $markValue = (int) $mark;

        // Check if mark already exists
        $sqlCheck = "SELECT Id FROM exam_results WHERE exam_id = '$examId' AND student_id = '$studentId'";
        $resultCheck = $db->query($sqlCheck);

        if ($resultCheck->num_rows == 0) {
            // Insert new mark
            $status = '';
            if ($markValue >= 75) {
                $status = 'A';
            } elseif ($markValue >= 65) {
                $status = 'B';
            } elseif ($markValue >= 55) {
                $status = 'C';
            } elseif ($markValue >= 35) {
                $status = 'S';
            } else{
                $status ='F';
            }
            
            $sqlInsert = "INSERT INTO exam_results (exam_id, student_id, marks,status) 
                         VALUES ('$examId', '$studentId', '$markValue','$status')";
            $db->query($sqlInsert);
        } else {
            // Update existing mark
            $sqlUpdate = "UPDATE exam_results SET marks = '$markValue' 
                         WHERE exam_id = '$examId' AND student_id = '$studentId'";
            $db->query($sqlUpdate);
        }
    }
}

// Handle attendance form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['exam_id']) && isset($_POST['students'])) {
    $examId = $_POST['exam_id'];
    $students = $_POST['students'];

    foreach ($students as $studentId => $attended) {
        $attendedVal = $attended == 'yes' ? 1 : 0;

        // Check if this record already exists
        $sqlCheck = "SELECT Id FROM exam_attendance WHERE exam_id = '$examId' AND student_id = '$studentId'";
        $resultCheck = $db->query($sqlCheck);

        if ($resultCheck->num_rows == 0) {

            // Only insert if not already present
            $sqlInsert = "INSERT INTO exam_attendance (exam_id, student_id, attended) 
                      VALUES ('$examId', '$studentId', '$attendedVal')";
            $db->query($sqlInsert);
        } else {

            // Optionally update if already exists
            $sqlUpdate = "UPDATE exam_attendance SET attended = '$attendedVal' 
                      WHERE exam_id = '$examId' AND student_id = '$studentId'";
            $db->query($sqlUpdate);
        }
    }
}

// Get selected exam to mark attendance 
$selectedExamId = isset($_POST['mark_exam_id']) ? $_POST['mark_exam_id'] : '';

// Fetch exams for today
$sql = "SELECT e.Id AS exam_id, e.type, c.Class_Name, e.date, e.status
        FROM exams e
        JOIN classes c ON e.class_id = c.Id 
        WHERE e.status = 'Completed' 
        AND c.Teacher_id = $teacherId
        AND e.date = CURDATE()";
$result = $db->query($sql);

?>

<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">Exam Marks</h3>
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Exam Type</th>
                    <th>Class Name</th>
                    <th>Exam Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0) { ?>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['type'] ?></td>
                            <td><?= $row['Class_Name'] ?></td>
                            <td><?= $row['date'] ?></td>
                            <td>
                                <form method="post" action="">
                                    <input type="hidden" name="mark_exam_id" value="<?= $row['exam_id'] ?>">
                                    <button type="submit" class="btn btn-success">Add Marks</button>
                                </form>
                            </td>
                        </tr>

                        <?php if ($selectedExamId == $row['exam_id']) { ?>
                            <tr>
                                <td colspan="4">
                                    <form method="post" action="">
                                        <input type="hidden" name="exam_id" value="<?= $row['exam_id'] ?>">
                                        <table class="table table-sm">
                                            <tr>
                                                <th>Reg No</th>
                                                <th>Student Name</th>
                                                <th>Marks</th>
                                                <th>Status</th>
                                            </tr>
                                            <?php
                                            $sqlStudents = "SELECT st.Id, u.FirstName, u.LastName, ea.attended, st.reg_no
                                                            FROM student_enroll se
                                                            JOIN students st ON se.student_id = st.Id
                                                            JOIN exam_attendance ea ON st.Id = ea.student_id
                                                            JOIN users u ON st.userid = u.Id
                                                            JOIN exams e ON e.class_id = se.class_id
                                                            WHERE e.Id = '$selectedExamId'";
                                            $resStudents = $db->query($sqlStudents);

                                            if ($resStudents->num_rows > 0) {
                                                while ($student = $resStudents->fetch_assoc()) {
                                            ?>
                                                    <?php
                                                    $readonly = '';
                                                    $bgrow = '';

                                                    if ($student['attended'] == '0') {
                                                        $readonly = 'readonly';
                                                        $bgrow = "bg-danger";
                                                    }
                                                    ?>
                                                    <tr class="<?= $bgrow ?>">
                                                        <td><?= $student['reg_no'] ?></td>
                                                        <td><?= $student['FirstName'] . ' ' . $student['LastName'] ?></td>
                                                        <td>
                                                            <input type="text" name="marks[]" <?= $readonly ?> class="marks-input" onkeyup="updateStatus(this)">
                                                            <input type="hidden" name="student_id[]" value="<?= $student['Id'] ?>">
                                                        </td>
                                                        <td class="status-cell">-</td>
                                                    </tr>
                                            <?php }
                                            } else {
                                                echo '<tr><td colspan="4">No students found for this exam.</td></tr>';
                                            }
                                            ?>
                                        </table>

                                        <button type="submit" class="btn btn-primary">Submit Marks</button>

                                    </form>
                                </td>
                            </tr>
                        <?php } ?>

                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="4" class="text-center">There are no exams today.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function updateStatus(input) {
        // Get the marks value
        const marks = parseInt(input.value);

        // Find the status cell in the same row
        const statusCell = input.closest('tr').querySelector('.status-cell');

        // Calculate and display status
        if (isNaN(marks) || input.value === '') {
            statusCell.textContent = '-';
        } else if (marks >= 75) {
            statusCell.textContent = 'A';
        } else if (marks >= 65) {
            statusCell.textContent = 'B';
        } else if (marks >= 55) {
            statusCell.textContent = 'C';
        } else if (marks >= 35) {
            statusCell.textContent = 'S';
        } else {
            statusCell.textContent = 'F';
        }
    }
</script>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>