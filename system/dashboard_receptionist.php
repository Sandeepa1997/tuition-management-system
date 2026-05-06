<?php

// Establish database connection
$db = dbConn();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $class_id = $_POST['class_id'];
    $status = $_POST['status'];


    $day = date('l');
    $date = date('Y-m-d');

    // Check if already inserted for today
    $checkSql = "SELECT * FROM daily_schedule WHERE class_id = '$class_id' AND date = '$date'";
    $checkResult = $db->query($checkSql);
    $rowresult = $checkResult->fetch_assoc();

    if ($checkResult->num_rows == 0) {

        // Insert new record
        $insertSql = "INSERT INTO daily_schedule (class_id, day, date, status)
                          VALUES ('$class_id', '$day', '$date', '$status')";
        $db->query($insertSql);
    } else {

        // Update existing record
        $updateSql = "UPDATE daily_schedule SET status = '$status' 
                          WHERE class_id = '$class_id' AND date = '$date'";
        $db->query($updateSql);
    }
}


$current_date = date('l');

$sql = "SELECT
    c.Id,
    c.Class_Date,
    c.month,
    c.start_time,
    c.end_time,
    G.name As Grade,
    s.name As Subject,
    t.FirstName,
    t.LastName
    
FROM
    classes AS c
LEFT JOIN grade_levels AS G ON c.Grade_Level_id = G.id
LEFT JOIN subjects AS s ON c.Subject_id = s.id
LEFT JOIN teachers AS t ON c.Teacher_id = t.Id

WHERE Class_Date = '$current_date'
ORDER BY c.Class_Date";

$result = $db->query($sql);






?>
<table class="table table-hover text-nowrap">
    <thead>
        <tr>

            <th>First Name</th>
            <th>Last Name</th>
            <th> Grade</th>
            <th> Class Date</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Subject</th>
            <th></th>
            <th></th>


        </tr>
    </thead>
    <tbody>

        <?php while ($row = $result->fetch_assoc()) {

            $classid = $row['Id'];
            $date = date('Y-m-d');

            // Fetch current status for this class/date
            $status = '';
            $sqlstatus = "SELECT status FROM daily_schedule WHERE class_id = '$classid' AND date = '$date'";
            $resultstatus = $db->query($sqlstatus);
            if ($resultstatus->num_rows > 0) {
                $rowresult_1 = $resultstatus->fetch_assoc();
                $status  = $rowresult_1['status'];
            }

        ?>
            <tr>

                <td><?= $row['FirstName'] ?></td>
                <td><?= $row['LastName'] ?></td>
                <td><?= $row['Grade'] ?></td>
                <td><?= $row['Class_Date'] ?></td>
                <td><?= date("g:i A", strtotime($row['start_time'])) ?></td>
                <td><?= date("g:i A", strtotime($row['end_time'])) ?></td>
                <td><?= $row['Subject'] ?></td>
                <td>
                    <form method="post" action="" id="formStatus<?= $row['Id'] ?>">
                        <input type="hidden" name="class_id" value="<?= $row['Id'] ?>">
                        <select name="status" class="form-select form-select-sm form-control">
                            <option value="">Select Status</option>
                            <option value="Started" <?= $status == 'Started' ? 'selected' : '' ?>>Started</option>
                            <option value="Cancelled" <?= $status == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            <option value="Postponed" <?= $status == 'Postponed' ? 'selected' : '' ?>>Postponed</option>
                            <option value="Ended" <?= $status == 'Ended' ? 'selected' : '' ?>>Ended</option>
                        </select>
                </td>
                <td>
                    <button type="submit" name="update_status" value="1" class="btn btn-sm btn-success">Proceed</button>
                    <?php
                    if ($status == 'Started') { ?>

                        <a href="attendance/mark_attendance.php ?class_id=<?= $row['Id'] ?>&date=<?= date('Y-m-d') ?>" class="btn btn-sm btn-warning">Mark Attendance</a>

                    <?php
                    } else {
                    }

                    ?>

                    </form>
                </td>

            </tr>
        <?php } ?>
    </tbody>
</table>
<?php
$current_year=date('Y');
$current_month=date('m');

?>
<a href="student_payment/run_paymonth.php?current_year=<?=$current_year?>&current_month=<?=$current_month?>">Proceed Pay Month</a>