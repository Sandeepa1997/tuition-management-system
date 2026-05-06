<?php
ob_start();
include '../../init.php';
?>

<div class="row ">
  <div class="col-md-12">

    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-user-plus" style="margin-right: 8px;"></i> Create New User Account</h3>
      </div>

      <head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

      </head>

      <?php

      extract($_POST);

      if ($_SERVER['REQUEST_METHOD'] == 'POST' && @$action == 'edit') {
        $sql = "SELECT * FROM users WHERE Id='$Id'";
        $db = dbConn();
        $result = $db->query($sql);

        $row = $result->fetch_assoc();

        $firstname = $row['FirstName'];
        $Lastname  = $row['LastName'];
        $email = $row['Email'];
        $nic_no = $row['NIC_No'];
        $gender = $row['Gender'];
        $user_role = $row['user_role'];
        $address = $row['Address'];
        $city = $row['City'];
        $contact_no1 = $row['Primary_Contact'];
        $contact_no2 = $row['Secondary_Contact'];
        $Id = $row['Id'];
      }


      //print_r($_POST);
      if ($_SERVER["REQUEST_METHOD"] == "POST" && $action == 'update') {


        $firstname = dataclean($firstname);
        $Lastname = dataclean($Lastname);
        $nicNo = dataclean($nic_no);
        $address = dataclean($address);
        $city = dataclean($city);
        $Email = dataclean($email);
        $contact_no1 = dataclean($contact_no1);
        $contact_no2 = dataclean($contact_no2);




        $messages = array();

        //Form required field validation

        //First Name
        if (empty($firstname)) {
          $messages['firstname'] = "The First Name should not be blanked!...";
        }

        //Last Name  
        if (empty($Lastname)) {
          $messages['Lastname'] = "The Last Name should not be blanked!...";
        }



        //Gender     
        if (empty($gender)) {
          $messages['gender'] = "Please select a gender!...";
        }

        //Address
        if (empty($address)) {
          $messages['address'] = "The Address should not be blanked!...";
        }

        //city
        if (empty($city)) {
          $messages['city'] = "The city should not be blanked!...";
        }

        //Email     
        if (empty($email)) {
          $messages['email'] = "The Email should not be blanked!...";
        }

        //Contact No         
        if (empty($contact_no1)) {
          $messages['contact_no1'] = "Please enter at least one contact number!...";
        }



        //Advanced Validation//

        //First Name Field
        if (!empty($firstname)) {
          if (ctype_alpha(str_replace(' ', '', $firstname)) === false) {
            $messages['firstname'] = "Only letters and white space allowed";
          }
        }

        //Last Name field
        if (!empty($Lastname)) {
          if (ctype_alpha(str_replace(' ', '', $Lastname)) === false) {
            $messages['Lastname'] = "Only letters and white space allowed";
          }
        }

        //Email Validation
        if (!empty($email)) {
          if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $messages['email'] = "The email address is invalid";
          }
        }

        //Check alredy exist in DB//

        //email
        if (!empty($email)) {
          $db = dbConn();
          $sql = "SELECT * FROM users WHERE Email ='$email' AND Id != '$Id'";
          $result = $db->query($sql);

          if ($result->num_rows > 0) {
            $messages['email'] = "The email address is already exsit!!!";
          }
        }

        //NIC
        if (!empty($nic)) {
          $db = dbConn();
          $sql = "SELECT * FROM users WHERE NIC_No ='$nic' AND Id != '$Id'";
          $result = $db->query($sql);

          if ($result->num_rows > 0) {
            $messages['nic'] = "The NIC is already exsit!!!";
          }
        }

        //Contact No 01
        if (!empty($contact_no1)) {
          $db = dbConn();
          $sql = "SELECT * FROM users WHERE  Primary_Contact ='$contact_no1' AND Id != '$Id'";
          $result = $db->query($sql);

          if ($result->num_rows > 0) {
            $messages['contact_no1'] = "The Contact Number is already exsit!!!";
          }
        }

        //Contact No 02
        if (!empty($contact_no2)) {
          $db = dbConn();
          $sql = "SELECT * FROM users WHERE Secondary_Contact ='$contact_no2' AND Id != '$Id'";
          $result = $db->query($sql);

          if ($result->num_rows > 0) {
            $messages['contact_no2'] = "The Contact Number is already exsit!!!";
          }
        }


        //Insert into database//

        if (empty($messages)) {
          $db = dbConn();
          $sql = "UPDATE users SET FirstName='$firstname', LastName='$Lastname', NIC_No='$nic_no', Email='$email', user_role ='$user_role',
    Address = '$address', Primary_Contact='$contact_no1', Secondary_Contact='$contact_no2'
     WHERE Id='$Id'";

          $db->query($sql);

          echo '<script>
          Swal.fire({
position: "center",
icon: "success",
title: "Your work has been updated",
showConfirmButton: false,
timer: 3500
}).then(function(){window.location="view.php"});
</script>';
        }
      }



      ?>


      <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" novalidate>

        <div class="card-body" style="background-color:rgb(174, 230, 176); color: black;">
          <div class="row  g-4">

            <!-- Left Column -->
            <div class="col-md-6">

              <!-- First Name -->
              <div class="form-group">
                <label for="firstName">First Name</label>
                <input type="text" class="form-control" id="firstName" placeholder="Enter First Name" name="firstname" value="<?= @$firstname ?>" style="background-color: rgb(213, 235, 209);">
                <span class="text-danger"><?= @$messages['firstname'] ?></span>
              </div>

              <!-- Last Name -->
              <div class="form-group">
                <label for="lastName">Last Name</label>
                <input type="text" class="form-control" id="lastName" placeholder="Enter Last Name" name="Lastname" value="<?= @$Lastname ?>" style="background-color: rgb(238, 247, 236);">
                <span class="text-danger"><?= @$messages['Lastname'] ?></span>
              </div>

              <!-- NIC No -->
              <div class="form-group">
                <label for="nicNo">NIC No</label>
                <input type="nic" class="form-control" id="nicNo" placeholder="Enter NIC No" name="nic_no" value="<?= @$nic_no ?>" style="background-color: rgb(238, 247, 236);">

              </div>


              <!-- Address -->
              <div class="form-group">
                <label for="address">Address</label>
                <textarea class="form-control" id="address" placeholder="Enter Address" name="address" style="background-color: rgb(238, 247, 236);"><?= @$address ?></textarea>
                <span class="text-danger"><?= @$messages['address'] ?></span><br>
                <input type="text" class="form-control" id="city" placeholder="City" name="city" value="<?= @$city ?>" style="background-color: rgb(238, 247, 236);">
                <span class="text-danger"><?= @$messages['city'] ?></span>
              </div>

              <!-- Gender -->
              <div class="form-group">
                <label for="gender">Gender</label><br>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="gender" id="male" value="male" <?php if (isset($gender) && $gender == 'male') {
                                                                                                      echo 'checked';
                                                                                                    } ?> style="background-color: rgb(238, 247, 236);">
                  <label class="form-check-label" for="male">Male</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="gender" id="female" value="female" <?php if (isset($gender) && $gender == 'female') {
                                                                                                          echo 'checked';
                                                                                                        } ?> style="background-color: rgb(238, 247, 236);"><br>
                  <label class="form-check-label" for="female">Female</label> <br>

                </div>
              </div>
              <span class="text-danger"><?= @$messages['gender'] ?></span>




            </div>

            <!-- Right Column -->
            <div class="col-md-6 ">


              <!-- Email -->
              <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" class="form-control" id="email" placeholder="Enter email" name="email" value="<?= @$email ?>" style="background-color: rgb(238, 247, 236);">
                <span class="text-danger"><?= @$messages['email'] ?></span>
              </div>

              <!-- Password-->
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" style="background-color: rgb(238, 247, 236);">
                <span class="text-danger"><?= @$messages['password'] ?></span>
              </div>

              <!-- Contact No -->
              <div class="form-group">
                <label for="contactNo">Contact No</label>
                <input type="tel" class="form-control" id="contactNo1" placeholder="Enter Primary Contact Number" name="contact_no1" value="<?= @$contact_no1 ?>" style="background-color: rgb(219, 248, 212);"><br>
                <input type="tel" class="form-control" id="contactNo2" placeholder="Enter Secondary Contact Number" name="contact_no2" value="<?= @$contact_no2 ?>" style="background-color: rgb(238, 247, 236);">
                <span class="text-danger"><?= @$messages['contact_no1'] ?></span>
              </div>

              <!-- Role -->
              <div class="form-group">
                <label for="role">Role</label>
                <?php
                $db = dbConn();
                $sql = "SELECT * FROM user_roles";
                $result = $db->query($sql);
                ?>
                <select class="form-control" id="user_role" name="user_role">
                  <option value="">Select the role</option>
                  <?php
                  if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                  ?>
                      <option value="<?= $row['Id'] ?>" <?= $row['Id'] ==  @$user_role ? 'selected' : '' ?>><?= $row['description'] ?></option>
                  <?php
                    }
                  }
                  ?>
                </select>
             
              </div>

              <!--Profile Picture    -->
              <div class="mb-3">
                <label for="profilepic" class="form-label">Profile Picture</label>
                <input class="form-control" type="file" id="profilepic" name="profilepic">
                <span class="text-danger"><?= @$messages['profilepic'] ?></span>
              </div>






            </div> <!-- End Row -->
          </div> <!-- End card-body -->


          <div class="card-footer  text-center" style="background-color:rgb(174, 230, 176); color: black;">
            <input type="hidden" name="Id" id="Id" value="<?= $Id ?>">
            <button type="submit" name="action" value="update" class="btn btn-primary">Submit</button>
          </div>

      </form>

    </div>




    <?php
    $content = ob_get_clean();
    include '../layouts.php';
    ?>