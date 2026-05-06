<?php
ob_start();
include '../../init.php';
?>

<div class="row">

    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th>Subject</th>
                <th>Grade</th>
                <th>Teacher</th>
                <th>Sentiment</th>
                <th>Feedback</th>
                <th>Rate</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $db = dbConn();
            $sql = "SELECT 
                    f.Id,
                    f.comment,
                    f.rate,
                    s.name AS Subject,
                    g.name AS Grade,
                    CONCAT(t.FirstName, ' ', t.LastName) AS Teacher
                FROM feedback f
                LEFT JOIN subjects s ON f.subject_id = s.id
                LEFT JOIN grade_levels g ON f.grade_level_id = g.id
                LEFT JOIN teachers t ON f.Teacher = t.Id";

            $result = $db->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {

                    $id = $row['Id'];

                    //python part to analyse the comment//
                    $msg = $row['comment'];

                    $msg_shell = escapeshellarg($msg);
                    $output = trim(shell_exec("C:\Users\MY-PC\AppData\Local\Programs\Python\Python313\python.exe sentiment.py $msg_shell 2>&1"));

                    $sql = "UPDATE feedback SET sentiment = '$output' WHERE Id = '$id' ";
                    $db->query($sql);
            ?>
                    <tr>
                        <td><?= $row['Subject'] ?></td>
                        <td><?= $row['Grade'] ?></td>
                        <td><?= $row['Teacher'] ?></td>
                        <td><?= $output ?></td>
                        <td><?= $row['comment'] ?></td>
                        <td>
                            <?php
                            if ($row['rate'] == '3') echo 'Excellent';
                            elseif ($row['rate'] == '2') echo 'Good';
                            elseif ($row['rate'] == '1') echo 'Bad';
                            ?>
                        </td>
                    </tr>
            <?php
                }
            }
            ?>
        </tbody>
    </table>


    <?php
    $content = ob_get_clean();
    include '../layouts.php';
    ?>