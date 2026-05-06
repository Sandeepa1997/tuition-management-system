<?php
ob_start();
include '../../init.php';


$where = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);

    if (!empty($nic)) {
        $where .= " NIC_No = '$nic' AND";
    }
    if (!empty($email)) {
        $where .= " Email = '$email' AND";
    }
    if (!empty($lastname)) {
        $where .= " LastName = '$lastname' AND";
    }

    if (!empty($where)) {
        $where = substr($where, 0, -3); 
        $where = " WHERE" . $where; 
}
}

// SQL query
$db = dbConn();
 $sql = "SELECT Id, FirstName, LastName, Email,NIC_No,user_role,Profile_Picture FROM users $where";

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
        <h1>View User Account</h1>
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title ">User List</h3>
            </div>
            <div class="card-body table-responsive p-0">

                <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                    <label for="">Enter NIC Number</label>
                    <input type="text" name="nic" id="nic">

                    <label for="">Enter Email Address</label>
                    <input type="text" name="email" id="email">

                        <label for=""> Enter Last Name</label>
                    <input type="text" name="lastname" id="lastname">

                    <button type="submit">Search</button>
                </form>
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th> Email</th>
                            <th> NIC</th>
                            <th>user role</th>
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
                                <td><?= $row['NIC_No'] ?></td>
                                <td><?= $row['user_role'] ?></td>
                                <td><img class="img-fluid" src="../uploads/<?= $row['Profile_Picture'] ?>" alt="" width="100"></td>
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