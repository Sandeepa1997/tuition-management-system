<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['ID'])) {
    header("Location: ../login.php");
    exit;
}

$db = dbConn();

// Fetch all classes with teacher, grade, subject
$sql = "SELECT 
            t.Id AS teacher_id,
            u.FirstName,
            u.LastName,
            s.name AS subject_name,
            g.name AS grade_name,
            c.class_fee
        FROM classes c
        INNER JOIN grade_levels g ON c.Grade_Level_id = g.id
        INNER JOIN subjects s ON c.Subject_id = s.id
        INNER JOIN teachers t ON c.Teacher_id = t.Id
        INNER JOIN users u ON t.userid = u.Id
        ORDER BY t.Id, s.id";

$result = $db->query($sql);

// Group data by teacher and subject
$data = [];
while ($row = $result->fetch_assoc()) {
    $teacherKey = $row['teacher_id'];
    $subjectKey = $row['subject_name'];

    if (!isset($data[$teacherKey])) {
        $data[$teacherKey] = [
            'teacher_name' => $row['FirstName'] . ' ' . $row['LastName'],
            'subjects' => []
        ];
    }

    if (!isset($data[$teacherKey]['subjects'][$subjectKey])) {
        $data[$teacherKey]['subjects'][$subjectKey] = [];
    }

    $data[$teacherKey]['subjects'][$subjectKey][] = [
        'grade' => $row['grade_name'],
        'fee' => $row['class_fee'],
        'share' => $row['class_fee'] * 0.10
    ];
}
?>

<div class="container mt-4">
    <h4 class="mb-3">Teacher Fee Summary (10% Commission)</h4>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Teacher</th>
                    <th>Subject</th>
                    <th>Grade</th>
                    <th>Class Fee (Rs.)</th>
                    <th>Teacher's Share (10%)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $teacher): ?>
                    <?php
                    $teacherRowspan = 0;
                    foreach ($teacher['subjects'] as $subject) {
                        $teacherRowspan += count($subject);
                    }
                    $firstTeacher = true;
                    foreach ($teacher['subjects'] as $subjectName => $classes) {
                        $subjectRowspan = count($classes);
                        $firstSubject = true;
                        foreach ($classes as $class) {
                ?>
                            <tr>
                                <?php if ($firstTeacher): ?>
                                    <td rowspan="<?= $teacherRowspan ?>" class="fw-bold"><?= $teacher['teacher_name'] ?></td>
                                    <?php $firstTeacher = false; ?>
                                <?php endif; ?>

                                <?php if ($firstSubject): ?>
                                    <td rowspan="<?= $subjectRowspan ?>"><?= $subjectName ?></td>
                                    <?php $firstSubject = false; ?>
                                <?php endif; ?>

                                <td><?= $class['grade'] ?></td>
                                <td>Rs. <?= number_format($class['fee'], 2) ?></td>
                                <td>Rs. <?= number_format($class['share'], 2) ?></td>
                            </tr>
                <?php
                        }
                    }
                    ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
