<?php
ob_start();
include '../../init.php';

//confirm whether login to the system
if (!isset($_SESSION['ID'])) {
    header("Location:../login.php");
}
?>



<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title"> Add new inventory items</h3>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        extract($_POST);

        $unit_price = dataClean($unitprice);
        $qty = dataClean($qty);
        $serialnum = dataClean($serialnum);
        $warranty = dataClean($warranty);

        $messages = array();

        //Form required field validation

        //Category
        if (empty($item_id)) {
            $messages['item_id'] = "Please select an item!";
        }

        //Supplier
        if (empty($supplier_id)) {
            $messages['supplier_id'] = "Please select a supplier!";
        }

        //Date
        if (empty($date)) {
            $messages['date'] = "Please select a date!";
        }

        if ($date > date('Y-m-d')) {
            $messages['date'] = "Date cannot be in the future!";
        }

        //Unit price
        if (empty($unitprice)) {
            $messages['unitprice'] = "Please select a Unit price!";
        }

        if($unitprice == 0 || $unitprice < 0 ){
            $messages['unitprice'] = "Please enter a valid unit price!";
        }

        //Qty
        if (empty($qty)) {
            $messages['qty'] = "Please select a Quantitiy!";
        }

        if($qty == 0 || $qty < 0 ){
            $messages['qty'] = "Please enter a valid quantity!";
        }

        //Check already exist in the database

        if (!empty($item_id) && !empty($supplier_id) && !empty($unitprice) && !empty($qty) && !empty($date)) {
            $db = dbConn();
            $sql = "SELECT * FROM item_inventory WHERE item_id='$item_id' AND supplier_id='$supplier_id' AND unit_price='$unitprice' AND qty='$qty' AND date='$date'";
            $result = $db->query($sql);

            if ($result->num_rows > 0) {
                $messages['item_id'] = "The Inventory Item already exisit...!";
            }
        }

        if (empty($messages)) {


            $db = dbConn();
           echo $sql = "INSERT INTO item_inventory(item_id, qty, date, unit_price, supplier_id, serial_number, warranty_period) VALUES
            ('$item_id','$qty','$date','$unitprice', '$supplier_id','$serialnum','$warranty')";
            $db->query($sql);

            echo '<script>
            Swal.fire({
position: "center",
icon: "success",
title: "Your work has been saved",
showConfirmButton: false,
timer: 3500
}).then(function(){window.location="view.php"});
</script>';
        }
    }

    ?>

    <!-- form start -->
    <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" novalidate enctype="multipart/form-data">
        <div class="card-body">
            <div class="form-group">
                <label for="">Item</label>
                <select name="item_id" id="item_id" class="form-control" onchange="loadSubCategory(this.value)">
                    <option value="">--</option>
                    <?php

                    $db = dbConn();
                    $sql = "SELECT * FROM items";
                    $result = $db->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                            <option value="<?= $row['Id'] ?>"><?= $row['item_name'] ?></option>
                    <?php
                        }
                    }
                    ?>
                </select>
                <span class="text-danger"><?= @$messages['item_id'] ?></span>

            </div>

            <!--Supplier -->
            <div class="form-group">
                <label for="">Supplier</label>
                <select name="supplier_id" id="supplier_id" class="form-control" onchange="showsupplier(this.value)">
                    <option value="">--</option>
                    <?php

                    $db = dbConn();
                    $sql = "SELECT * FROM suppliers";
                    $result = $db->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                            <option value="<?= $row['Id'] ?>"><?= $row['StoreName'] ?></option>
                    <?php
                        }
                    }
                    ?>
                </select>
                <span class="text-danger"><?= @$messages['supplier_id'] ?></span>

            </div>
        <div id="supplier_data"></div>

                <!--Date -->
                <div class="form-group mt-3">
                    <label for="date"> Date</label>
                    <input type="date" class="form-control" id="date" name="date" value="<?= @$date ?>">
                    <span class="text-danger"><?= @$messages['date'] ?></span>
                </div>

                <!--Unit price -->
                <div class="form-group mt-3">
                    <label for="unitprice"> Unit Price</label>
                    <input type="text" class="form-control" id="unitprice" name="unitprice" value="<?= @$unitprice ?>">
                    <span class="text-danger"><?= @$messages['unitprice'] ?></span>
                </div>

                <!--Qty -->
                <div class="form-group mt-3">
                    <label for="Qty">Qty </label>
                    <input type="number" class="form-control" id="qty" name="qty" value="<?= @$qty ?>">
                    <span class="text-danger"><?= @$messages['qty'] ?></span>
                </div>

                <!--Serial-number -->
                <div class="form-group mt-3">
                    <label for="serialnum">Serial-Number </label>
                    <input type="text" class="form-control" id="serialnum" name="serialnum" value="<?= @$serialnum ?>">

                </div>

                <!--Warranty-Period-->
                <div class="form-group mt-3">
                    <label for="warranty">Warranty Period </label>
                    <input type="text" class="form-control" id="warranty" name="warranty" value="<?= @$warranty ?>">

                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>


    </form>

    <script>
         function showsupplier (supplier_id){
        $.ajax({
            type:'POST',
            url:'get_supplier.php',
            data:{supplier_id:supplier_id 

            },
            success:function(response){
               $('#supplier_data').html(response)
            }

        })
    }
    </script>

    <?php
    $content = ob_get_clean();
    include '../layouts.php';
    ?>