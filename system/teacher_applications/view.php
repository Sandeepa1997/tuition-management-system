<?php
ob_start();
include '../../init.php';

// Establish database connection
$db = dbConn();

// SQL query
$sql = "SELECT
    t.Id,
    t.FirstName,
    t.LastName,
    t.Email,
    t.NIC_No,
    t.Primary_Contact,
    t.Profile_Picture,
    t.date,
    ud.university_name,
    ud.degree_name,
    ud.degree_class,
    si.School_Institute_name,
    si.type,
    si.duration
FROM
    teachers AS t
LEFT JOIN
    teacher_degrees AS ud ON t.id = ud.Id
LEFT JOIN
    teacher_experience AS si ON t.id = si.Id";


$result = $db->query($sql);

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
        <h1>View Teacher Applications</h1>
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title ">Application List</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Primary Contact</th>
                            <th>Date</th>
                            <th>Profile Picture</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $row['Id'] ?></td>
                                <td><?= $row['FirstName'] ?></td>
                                <td><?= $row['LastName'] ?></td>
                                <td><?= $row['Email'] ?></td>
                                <td><?= $row['Primary_Contact'] ?></td>

                                <td><?= $row['date'] ?></td>


                                <td><img class="img-fluid" src="../uploads/<?= $row['Profile_Picture'] ?>" alt="" width="100"></td>
                                <td>
                                    <form action="view_full.php" method="post" id="frmview<?= $row['Id'] ?>">
                                        <input type="hidden" name="Id" id="Id<?= $row['Id'] ?>" value="<?= $row['Id'] ?>">
                                        <button type="submit" name="action" value="view" class="btn btn-info"> <i class="fa-solid fa-magnifying-glass"></i></button>
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