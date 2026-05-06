<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['ID'])) {
    header("Location:../login.php");
    exit;
}

$teacherId = $_SESSION['TEACHER_ID']; 
$where = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);

    if (!empty($regno)) {
        $where .= " s.reg_no = '$regno' AND";
    }

    if (!empty($grade)) {
        $where .= " g.name = '$grade' AND";
    }

    if (!empty($title)) {
        $where .= " q.title = '$title' AND";
    }

    if (!empty($where)) {
        $where = " AND " . substr($where, 0, -3); 
    }
}

$teacherId = $_SESSION['TEACHER_ID']; 
 $db =dbConn();
$sql = "SELECT 
            s.reg_no,
            u.FirstName,
            u.LastName,
            g.name AS grade,
            q.title,
            t.term,
            qa.score,
            qa.num_of_questions,
            qa.attempted_at
        FROM quiz_attempts qa
        INNER JOIN (
            SELECT student_id, quiz_id, MAX(score) AS max_score
            FROM quiz_attempts
            GROUP BY student_id, quiz_id
        ) max_scores 
        ON qa.student_id = max_scores.student_id 
           AND qa.quiz_id = max_scores.quiz_id 
           AND qa.score = max_scores.max_score
        INNER JOIN quizzes q ON qa.quiz_id = q.id
        JOIN terms t ON q.term_id = t.Id
        INNER JOIN classes c ON q.class_id = c.id
        INNER JOIN grade_levels g ON c.Grade_Level_id = g.id
        INNER JOIN students s ON qa.student_id = s.id
        INNER JOIN users u ON s.userid = u.Id
        WHERE c.Teacher_id = '$teacherId'
        $where";


$result = $db->query($sql);
?>

<html>
<head>
    <title>Quiz Search</title>
</head>
<body>
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-body">
                    <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                        <label for="regno">Enter Reg No</label>
                        <input type="text" name="regno" id="regno">

                        <label for="grade">Enter Grade</label>
                        <input type="text" name="grade" id="grade">

                        <label for="title">Enter Quiz Title</label>
                        <input type="text" name="title" id="title">

                        <button type="submit">Search</button>
                    </form>
                </div>

            <div class="card-header bg-success">
                <h3 class="card-title">Results</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr> <th>Term</th>
                            <th>Reg No</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Grade</th>
                            <th>Quiz Title</th>
                            <th>Score (Out of 100%)</th>
                            <th>Attempted At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()) {
                                $percentage = round(($row['score'] / $row['num_of_questions']) * 100, 2);

                            ?>
                                <tr><td><?= $row['term'] ?></td>
                                    <td><?= $row['reg_no'] ?></td>
                                    <td><?= $row['FirstName'] ?></td>
                                    <td><?= $row['LastName'] ?></td>
                                    <td><?= $row['grade'] ?></td>
                                    <td><?= $row['title'] ?></td>
                                    <td><?= number_format($percentage, 2) ?>%</td>
                                    <td><?= date('F j, Y - g:i A', strtotime($row['attempted_at'])) ?></td>
                                </tr>
                            <?php } ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">No results found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
