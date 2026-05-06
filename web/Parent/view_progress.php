<?php
ob_start();
include '../../init.php';

$messages = array();
$regno = $_POST['regno'] ?? null;
$resultMarks = null;

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
                       //Fetch exam results
                        $sqlResults = "SELECT er.marks, t.term AS exam_term, cls.Class_Name, u.FirstName, u.LastName
                                       FROM exam_results er
                                       JOIN exams e ON er.exam_id = e.Id
                                       JOIN terms t ON e.term_id = t.Id
                                       JOIN students s ON er.student_id = s.Id
                                       JOIN users u ON s.userid = u.Id
                                       JOIN classes cls ON e.class_id = cls.Id
                                       WHERE er.student_id = '$studentId'";
                        $resultMarks = $db->query($sqlResults);
                    }
                }
            }
        }
    }
}
?>


<div class="container mt-5">
    <h4 class="mb-4">View Child's Progress & Marks</h4>

    <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="p-4 border rounded shadow-sm bg-light mb-4">
        <div class="mb-3">
            <label for="regno" class="form-label fw-bold">Student Registration Number</label>
            <input type="text" name="regno" id="regno" class="form-control" placeholder="e.g., R123456" value="<?= htmlspecialchars($regno) ?>">
            <span class="text-danger" style="font-size: 13px;"><?= @$messages['regno'] ?></span>
        </div>
        <div class="text-end">
            <button type="submit" class="btn btn-primary px-4">Submit</button>
        </div>
    </form>

    <?php if (isset($resultMarks) && $resultMarks->num_rows > 0): ?>
        <h4>Exam Results for <strong><?= htmlspecialchars($regno) ?></strong></h4>
        <table class="table table-bordered">
            <thead class="table-success">
                <tr>
                    <th>Name</th>
                    <th>Term</th>
                    <th>Class</th>
                    <th>Marks</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $resultMarks->fetch_assoc()): ?>
                    <?php
                        $status = '';
                        $rowClass = '';

                        if ($row['marks'] === null || strtolower($row['marks']) === 'null') {
                            $status = 'Absent';
                            $rowClass = 'bg-danger';
                        } elseif ($row['marks'] >= 75) {
                            $status = 'A';
                            $rowClass = 'bg-success';
                        } elseif ($row['marks'] >= 65) {
                            $status = 'B';
                            $rowClass = 'style="background-color:rgb(116, 245, 66);"';
                        } elseif ($row['marks'] >= 55) {
                            $status = 'C';
                            $rowClass = 'style="background-color:rgb(116, 245, 66);"';
                        } elseif ($row['marks'] >= 35) {
                            $status = 'S';
                            $rowClass = 'bg-warning';
                        } else {
                            $status = 'F';
                            $rowClass = 'bg-danger';
                        }
                    ?>
                    <tr <?= strpos($rowClass, 'style=') === 0 ? $rowClass : 'class="' . $rowClass . '"' ?>>
                        <td><?= $row['FirstName'] ?> <?= $row['LastName'] ?></td>
                        <td><?= $row['exam_term'] ?></td>
                        <td><?= $row['Class_Name'] ?></td>
                        <td><?= $row['marks'] === null || strtolower($row['marks']) === 'null' ? 'Absent' : $row['marks'] ?></td>
                        <td><?= $status ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($messages)): ?>
        <div class="alert alert-info">No exam results found for this student.</div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
