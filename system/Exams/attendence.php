<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['TEACHER_ID'])) {
    header('Location: ../login.php');
    exit;
}

$db = dbConn();
$teacherId = $_SESSION['TEACHER_ID'];

// Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['exam_id']) && isset($_POST['students'])) {
    $examId = $_POST['exam_id'];
    $students = $_POST['students'];

    foreach ($students as $studentId => $attended) {
        $attendedVal = ($attended == 'yes') ? 1 : 0;

        $sqlCheck = "SELECT Id FROM exam_attendance WHERE exam_id = '$examId' AND student_id = '$studentId'";
        $resultCheck = $db->query($sqlCheck);

        if ($resultCheck->num_rows == 0) {
            $sqlInsert = "INSERT INTO exam_attendance (exam_id, student_id, attended) 
                          VALUES ('$examId', '$studentId', '$attendedVal')";
            $db->query($sqlInsert);
        } else {
            $sqlUpdate = "UPDATE exam_attendance SET attended = '$attendedVal' 
                          WHERE exam_id = '$examId' AND student_id = '$studentId'";
            $db->query($sqlUpdate);
        }
    }
}

$selectedExamId = $_POST['mark_exam_id'] ?? null;

$sql = "SELECT e.Id AS exam_id, e.term_id, t.term, c.Class_Name, e.date, e.status
        FROM exams e
        JOIN classes c ON e.class_id = c.Id 
        JOIN terms t ON e.term_id = t.Id
        WHERE e.status = 'Scheduled' 
        AND c.Teacher_id = $teacherId
        ";
$result = $db->query($sql);
?>

<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">Mark Exam Attendance</h3>
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Term</th>
                    <th>Class Name</th>
                    <th>Exam Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0) { ?>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['term'] ?></td>
                            <td><?= $row['Class_Name'] ?></td>
                            <td><?= $row['date'] ?></td>
                            <td>
                                <form method="post" action="">
                                    <input type="hidden" name="mark_exam_id" value="<?= $row['exam_id'] ?>">
                                    <input type="hidden" name="term_id" value="<?= $row['term_id'] ?>">
                                    <button type="submit" class="btn btn-success">Mark Attendance</button>
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
                                                <th>Student Name</th>
                                                <th>Attendance</th>
                                            </tr>
                                            <?php
                                            $sqlStudents = "SELECT st.Id, u.FirstName, u.LastName, ea.attended
                                                            FROM student_enroll se
                                                            JOIN students st ON se.student_id = st.Id
                                                            JOIN users u ON st.userid = u.Id
                                                            JOIN exams e ON e.class_id = se.class_id
                                                            LEFT JOIN exam_attendance ea 
                                                                ON ea.student_id = st.Id AND ea.exam_id = '$selectedExamId'
                                                            WHERE e.Id = '$selectedExamId'";
                                            $resStudents = $db->query($sqlStudents);

                                            if ($resStudents->num_rows > 0) {
                                                while ($student = $resStudents->fetch_assoc()) {
                                                    $attendedVal = $student['attended'];
                                            ?>
                                                    <tr>
                                                        <td><?= $student['FirstName'] . ' ' . $student['LastName'] ?></td>
                                                        <td>
                                                            <label>
                                                                <input type="radio" name="students[<?= $student['Id'] ?>]" value="yes" <?= $attendedVal === '1' ? 'checked' : '' ?>> Present
                                                            </label>
                                                            <label>
                                                                <input type="radio" name="students[<?= $student['Id'] ?>]" value="no" <?= $attendedVal === '0' ? 'checked' : '' ?>> Absent
                                                            </label>
                                                        </td>
                                                    </tr>
                                            <?php }
                                            } else {
                                                echo '<tr><td colspan="2">No students found for this exam.</td></tr>';
                                            }
                                            ?>
                                        </table>
                                        <button type="submit" class="btn btn-primary">Submit Attendance</button>
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

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
