<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['TEACHER_ID'])) {
    header('Location: ../login.php');
    exit;
}

$db = dbConn();
$teacherId = $_SESSION['TEACHER_ID'];


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['exam_id']) && isset($_POST['marks']) && isset($_POST['student_id']) && isset($_POST['term_id'])) {
    $examId = $_POST['exam_id'];
    $marks = $_POST['marks'];
    $studentIds = $_POST['student_id'];
    $termId = $_POST['term_id'];


    foreach ($studentIds as $key => $studentId) {
        $mark = $marks[$key];

        if ($mark != '') {
            $sql = "SELECT Id FROM exam_results WHERE exam_id = '$examId' AND student_id = '$studentId'";
            $result = $db->query($sql);

            if ($result->num_rows == 0) {
                $sqlInsert = "INSERT INTO exam_results (exam_id, student_id,marks,term_id) 
                              VALUES ('$examId', '$studentId', '$mark','$termId')";
                $db->query($sqlInsert);
            } else {
                $sqlUpdate = "UPDATE exam_results SET marks = '$mark' 
                              WHERE exam_id = '$examId' AND student_id = '$studentId'";
                $db->query($sqlUpdate);
            }
        }
    }
}





// Get selected exam for marks form
$selectedExamId = isset($_POST['mark_exam_id']) ? $_POST['mark_exam_id'] : null;

// Fetch today's completed exams for this teacher
$sql = "SELECT e.Id AS exam_id, t.term,t.Id AS term_id,c.Class_Name, e.date, e.status
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
        <h3 class="card-title">Exam Marks</h3>
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

                                    <button type="submit" class="btn btn-success">Add Marks</button>
                                </form>
                            </td>
                        </tr>

                        <?php if ($selectedExamId == $row['exam_id']) { ?>
                            <tr>
                                <td colspan="4">
                                    <form method="post" action="">
                                        <input type="hidden" name="exam_id" value="<?= $row['exam_id'] ?>">
                                        <input type="hidden" name="term_id" value="<?= $row['term_id'] ?>">

                                        <table class="table table-sm">
                                            <tr>
                                                <th>Reg No</th>
                                                <th>Student Name</th>
                                                <th>Marks</th>
                                            </tr>
                                            <?php

                                            $sqlStudents = "SELECT st.Id, u.FirstName, u.LastName, ea.attended, st.reg_no
                                                            FROM exams e
                                                             JOIN student_enroll se ON e.class_id = se.class_id
                                                             JOIN students st ON se.student_id = st.Id
                                                             JOIN users u ON st.userid = u.Id
                                                             LEFT JOIN exam_attendance ea ON st.Id = ea.student_id AND ea.exam_id = e.Id
                                                             WHERE e.Id = '$selectedExamId'";

                                            $resStudents = $db->query($sqlStudents);

                                            if ($resStudents->num_rows > 0) {
                                                while ($student = $resStudents->fetch_assoc()) {

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
                                                            <?php if ($student['attended'] == '0') { ?>
                                                                <input type="text" class="form-control" value="null" readonly>
                                                                <input type="hidden" name="marks[]" value="null">
                                                            <?php } else { ?>
                                                                <input type="text" name="marks[]" class="form-control">
                                                            <?php } ?>

                                                            <input type="hidden" name="student_id[]" value="<?= $student['Id'] ?>">
                                                        </td>
                                                    </tr>
                                            <?php
                                                }
                                            } else {
                                                echo '<tr><td colspan="3">No students found for this exam.</td></tr>';
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

<?php
$content = ob_get_clean();
include '../layouts.php';
?>