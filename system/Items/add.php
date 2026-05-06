<?php
ob_start();
include '../../init.php';
if (!isset($_SESSION['ID'])) {
    header("Location:../login.php");
}
?>

<div class="card">
    <div class="card-header bg-info">
        <h4>Add Items</h4>
    </div>

    <?php

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        extract($_POST);

        $itemName = dataClean($itemName);
        $ReorderLevel = dataClean($ReorderLevel);

        $messages = array();

        //Empty Validation
        /*  if (empty($itemtName)) {
            $messages['itemtName'] = "The item Name Should not be blank...!";
        }

        if (empty($ReorderLevel)) {
            $messages['ReorderLevel'] = "The Re-order Should not be blank...!";
        }*/

        $file_name = $_FILES['itemImage']['name'];
        $file_tmp = $_FILES['itemImage']['tmp_name'];
        $file_size = $_FILES['itemImage']['size'];
        $file_error = $_FILES['itemImage']['error'];

        if (!empty($file_name)) {
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            //allowed file types
            $file_types = ['jpg', 'jpeg', 'gif', 'png', 'pdf'];
            if (in_array($file_ext, $file_types)) {
                if ($file_error === 0) {
                    if ($file_size <= 2097152) {
                        $file_new_name = uniqid('', true) . '.' . $file_ext;
                        $file_location = '../uploads/' . $file_new_name;
                        //move_uploaded_file($file_tmp, $file_location);
                    } else {
                        $messages['itemImage'] = "The file is too large. Maximum is is 2MB";
                    }
                } else {
                    $messages['itemImage'] = "There was an unknown upload error.";
                }
            } else {
                $messages['itemImage'] = "This file type not allowed";
            }
        } else {
            $messages['itemImage'] = "The Image should be select...!";
        }

        if (empty($messages)) {

            //Insert into the database;
            $db = dbConn();

            $sql = "INSERT INTO items(item_name,reorder_level,item_image,category,sub_category) VALUES 
            ('$itemName','$ReorderLevel','$file_new_name','$category','$sub_category')";


            $db->query($sql);
            $item_id = $db->insert_id;

            $i = 0;
            foreach ($spec as $val) {

                $spec_val = $value[$i];
                $status = $Status[$i];
                $sql = "INSERT INTO item_specification(specification,value,item_id,Status)
                VALUES('$val','$spec_val','$item_id','$status')";
                $db->query($sql);


                $i++;
            }
        }
    }







    ?>
    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post" enctype="multipart/form-data">
        <div class="card-body">
            <div class="form-group">
                <label for="">item Name</label>
                <input type="text" class="form-control" name="itemName" id="itemName">
            </div>

            <div class="form-group">
                <label for="">Category</label>
                <select name="category" id="category" class="form-control" onchange="loadSubCategory(this.value)">
                    <option value="">--</option>
                    <?php

                    $db = dbConn();
                    $sql = "SELECT * FROM categories";
                    $result = $db->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                            <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                    <?php
                        }
                    }
                    ?>
                </select>

            </div>

            <div class="form-group">
                <label for=""> Sub-Category</label>
                <select name="sub_category" id="sub_category" class="form-control">
                    <option value="">--</option>
                    <?php

                    $db = dbConn();
                    $sql = "SELECT * FROM subcategories";
                    $result = $db->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                            <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                    <?php
                        }
                    }
                    ?>
                </select>

            </div>







            <div class="form-group">
                <label for="">Re-Order Level</label>
                <input type="text" class="form-control" name="ReorderLevel" id="ReorderLevel">
            </div>
            <div class="form-group">
                <label for="">Item Image</label>
                <input type="file" class="form-control" name="itemImage" id="itemImage">
            </div>




            <table class="table table-striped" id="itemspec">
                <thead>
                    <tr>
                        <th>Specification</th>
                        <th>Value</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <input type="text" name="spec[]" class="form-control">
                        </td>
                        <td>
                            <input type="text" name="value[]" class="form-control">
                        </td>
                        <td>
                            <select class="form-control" name="Status[]">
                                <option value="">--</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger removeRow">Remove</button>
                        </td>

                    </tr>
                </tbody>

            </table>
            <button type="button" class="btn btn-success btn-sm mt-2" id="addRow">Add More Spec</button>

        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableBody = document.querySelector('#itemspec tbody');
        const addRowBtn = document.getElementById('addRow');

        addRowBtn.addEventListener('click', () => {
            const newRow = document.createElement('tr');
            newRow.innerHTML = '<td><input type="text" name="spec[]" class="form-control"></td><td><input type="text" name="value[]" class="form-control"></td><td><select class="form-control" name="Status[]"><option value ="" > -- </option> <option value = "1" > Active </option> <option value = "0" > Inactive</option></select></td><td><button type="button" class="btn btn-danger removeRow"> Remove </button></td>';

            tableBody.appendChild(newRow);

        });

        tableBody.addEventListener('click', (event) => {
            if (event.target && event.target.classList.contains('removeRow')) {
                const row = event.target.closest('tr');
                if (tableBody.rows.length > 1) {
                    row.remove();
                }

            }
        })


    })

    function loadSubCategory(categoryid) {
        $.ajax({
            type: 'POST',
            url: 'get_subcategories.php',
            data: {
                cat_id: categoryid

            },
            success: function(response) {
                $('#sub_category').html(response)
            }

        })
    }
</script>





<?php
$content = ob_get_clean();
include '../layouts.php';
?>