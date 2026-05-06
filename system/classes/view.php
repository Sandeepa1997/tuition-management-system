<?php
ob_start();
include '../../init.php';
?>

<?php



$where = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);

    if (!empty($grade_id)) {
        $where .= "  c.Grade_Level_id  = '$grade_id' AND";
    }
    if (!empty($subject_id)) {
        $where .= " c.Subject_id   = '$subject_id' AND";
    }
    if (!empty($where)) {
        $where = substr($where, 0, -3);
        $where = " WHERE" . $where;
    }
}
// Establish database connection
$db = dbConn();

// SQL query
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

$where
ORDER BY c.Class_Date DESC";

$result = $db->query($sql);

// Get current month and year
$currentMonthName = date('F');
$currentYear = date('Y');    

?>
<html>

<head>
    <style>
        th,
        tbody {
            background-color: rgb(174, 230, 176);

        }
    </style>


</head>

<div class="row">
    <div class="col-md-12">
        <h1>View Class Schedules</h1>
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title ">Class List</h3>
            </div>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="row mb-3 container">

                    <!-- Grade Dropdown -->
                    <div class="col-md-6">
                        <label for="grade_id" class="form-label">Grade</label>
                        <select class="form-control" id="grade_id" name="grade_id">

                            <option value="">Select the Grade</option>
                            <?php
                            $db = dbConn();
                            $sqlgrade = "SELECT * FROM grade_levels";
                            $resultgrade = $db->query($sqlgrade);
                            if ($resultgrade->num_rows > 0) {
                                while ($rowgrade = $resultgrade->fetch_assoc()) {
                            ?>
                                    <option value="<?php echo $rowgrade['id']; ?>" <?php if (@$grade_id == $rowgrade['id']) echo 'selected'; ?>>
                                        <?php echo $rowgrade['name']; ?>
                                    </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Subject Dropdown -->
                    <div class="col-md-6">
                        <label for="subject_id" class="form-label">Subject</label>
                        <select class="form-control" id="subject_id" name="subject_id">
                            <option value="">Select the Subject</option>
                            <?php
                            $sqlsubject = "SELECT * FROM subjects";
                            $resultsubject = $db->query($sqlsubject);
                            if ($resultsubject->num_rows > 0) {
                                while ($rowsubject = $resultsubject->fetch_assoc()) {
                            ?>

                                    <option value="<?php echo $rowsubject['id']; ?>" <?php if (@$subject_id == $rowsubject['id']) echo 'selected'; ?>>
                                        <?php echo $rowsubject['name']; ?>
                                    </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>

                </div>

                <button type="submit" class="btn btn-success my-3 mx-2">Search</button>
            </form>

            <div class="card-body table-responsive p-0">
                <h3><?=$currentMonthName ?>-<?=$currentYear?></h3>
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

                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>

                                <td><?= $row['FirstName'] ?></td>
                                <td><?= $row['LastName'] ?></td>
                                <td><?= $row['Grade'] ?></td>
                                <td><?= $row['Class_Date'] ?></td>
                                <td><?= date("g:i A", strtotime($row['start_time'])) ?></td>
                                <td><?= date("g:i A", strtotime($row['end_time'])) ?></td>
                                <td><?= $row['Subject'] ?></td>
                             


                                <td>
                                    <form action="edit.php" method="post" id="frmedit<?= $row['Id'] ?>">
                                        <input type="hidden" name="Id" id="Id<?= $row['Id'] ?>" value="<?= $row['Id'] ?>">
                                        <button type="submit" name="action" value="edit" class="btn btn-primary"><i class="fas fa-edit"></i></button>
                                    </form>
                                </td>
                                <td>
                                    <form action="delete.php" method="post" id="frm<?= $row['Id'] ?>">
                                        <input type="hidden" name="Id" id="Id<?= $row['Id'] ?>" value="<?= $row['Id'] ?>">
                                        <button type="button" onclick="confirmDelete(<?= $row['Id'] ?>)" name="action" value="delete" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    function confirmDelete(id) {

        let result = confirm("Are you sure want to delete?");
        if (result) {
            document.getElementById('frm' + id).submit();

        }
    }
</script>

</html>



<?php
$content = ob_get_clean();
include '../layouts.php';
?>