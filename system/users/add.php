<?php
ob_start();
include '../../init.php';

//confirm whether login to the system
if (!isset($_SESSION['ID'])) {
  header("Location:../login.php");
}
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

      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        extract($_POST);

        $firstname = dataclean($firstname);
        $Lastname = dataclean($Lastname);
        $nicNo = dataclean($nic_no);
        $address = dataclean($address);
        $Email = dataclean($email);
        $contact_no1 = dataclean($contact_no1);
        $contact_no2 = dataclean($contact_no2);
        $password = dataclean($password);



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


        //Nic No     
        if (empty($nic_no)) {
          $messages['nic_no'] = "The NIC No should not be blanked!...";
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

        //Role
        if (empty($user_role)) {
          $messages['user_role'] = "Please select a role!...";
        }

        //Password      
        if (empty($password)) {
          $messages['password'] = "The password should not be blanked!...";
        }

        //Password length validation
        if (!empty($password)) {

          if (strlen($password) < 8) {
            $messages['password'] = "Password must be at least 8 characters long...!";
          } elseif (!preg_match('/[A-Z]/', $password)) {
            $messages['password'] = "Password must contains at least one uppercase letter...!";
          } elseif (!preg_match('/[a-z]/', $password)) {
            $messages['password'] = "Password must contains at least one lowercase letter...!";
          } elseif (!preg_match('/[0-9]/', $password)) {
            $messages['password'] = "Password must contains at least one  number...!";
          } elseif (!preg_match('/[\W_]/', $password)) {
            $messages['password'] = "Password must contains at least one  special charcter...!";
          }
        }

        //File upload
        $file_name = $_FILES['profilepic']['name'];
        $file_tmp = $_FILES['profilepic']['tmp_name'];
        $file_size = $_FILES['profilepic']['size'];
        $file_error = $_FILES['profilepic']['error'];


        if (!empty($file_name)) {
          $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

          //allowed file types
          $file_types = ['jpg', 'jpeg', 'png', 'gif', 'avif'];

          if (in_array($file_ext, $file_types)) {

            if ($file_error === 0) {

              if ($file_size <= 2097152) {

                $file_newName = uniqid('', true) . '.' . $file_ext;
                $file_location = '../uploads/' . $file_newName;
                move_uploaded_file($file_tmp, $file_location);
              } else {
                $messages['profilepic'] = "The file is too large.Maximum size is 2MB..!";
              }
            } else {
              $messages['profilepic'] = "unknown error occured..!";
            }
          } else {
            $messages['profilepic'] = "File type is not allowed..!";
          }
        } else {
          $messages['profilepic'] = "Profile picture should be selected...!";
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
          $sql = "SELECT * FROM users WHERE Email ='$email'";
          $result = $db->query($sql);

          if ($result->num_rows > 0) {
            $messages['email'] = "The email address is already exsit!!!";
          }



          //NIC length validation
          function isValidNIC($nic)
          {

            $len = strlen($nic);

            if ($len === 10) {
              $digits = substr($nic, 0, 9);
              $lastChar = strtoupper($nic[9]);
              return ctype_digit($digits) && ($lastChar === 'V' || $lastChar === 'X');
            } elseif ($len === 12) {
              return ctype_digit($nic);
            }
            return false;
          }

          $NIC = isValidNIC($nic);

          if ($NIC == false) {
            $messages['nic'] = "Invalid NIC number format";
          }

          
          //NIC
          if (!empty($nic)) {
            $db = dbConn();
            $sql = "SELECT * FROM users WHERE NIC_No ='$nic'";
            $result = $db->query($sql);

            if ($result->num_rows > 0) {
              $messages['nic'] = "The NIC is already exsit!!!";
            }
          }

          //Contact No 01
          if (!empty($contact_no1)) {
            $db = dbConn();
            $sql = "SELECT * FROM users WHERE  Primary_Contact ='$contact_no1'";
            $result = $db->query($sql);

            if ($result->num_rows > 0) {
              $messages['contact_no1'] = "The Contact Number is already exsit!!!";
            }
          }

          //Contact No 02
          if (!empty($contact_no2)) {
            $db = dbConn();
            $sql = "SELECT * FROM users WHERE Secondary_Contact ='$contact_no2'";
            $result = $db->query($sql);

            if ($result->num_rows > 0) {
              $messages['contact_no2'] = "The Contact Number is already exsit!!!";
            }
          }



          //Insert into database//
          if (empty($messages)) {

            $password = password_hash($password, PASSWORD_DEFAULT);
            $db = dbConn();
            $sql = "INSERT INTO users (
              FirstName, LastName, NIC_No, Gender, Address, City, Email,Password,
              Primary_Contact, Secondary_Contact, user_role,  
              Profile_Picture
          ) 
          VALUES (
              '$firstname', '$Lastname', '$nic_no', '$gender', '$address', '$city', '$email',
              '$password','$contact_no1', '$contact_no2', '$user_role','$file_newName'
              
          )";

            $db->query($sql);

            echo '<script>
          Swal.fire({
    position: "center",
    icon: "success",
    title: "Your work has been saved",
    showConfirmButton: false,
    timer: 3500
    }).then(function(){window.location="add.php"});
    </script>';
          }
        }
      }


      ?>

      <div class="container mt-5">
        <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" novalidate enctype="multipart/form-data">
          <div class="card">
            <div class="card-body" style="background-color:rgb(174, 230, 176); color: black;">
              <div class="row g-4">

                <!-- Left Column -->
                <div class="col-md-6">

                  <!-- First Name -->
                  <div class="mb-3">
                    <label for="firstName" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="firstName" name="firstname" placeholder="Enter First Name" value="<?= @$firstname ?>" style="background-color: rgb(213, 235, 209);">
                    <span class="text-danger"><?= @$messages['firstname'] ?></span>
                  </div>

                  <!-- Last Name -->
                  <div class="mb-3">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="lastName" name="Lastname" placeholder="Enter Last Name" value="<?= @$Lastname ?>" style="background-color: rgb(238, 247, 236);">
                    <span class="text-danger"><?= @$messages['Lastname'] ?></span>
                  </div>

                  <!-- NIC No -->
                  <div class="mb-3">
                    <label for="nicNo" class="form-label">NIC No</label>
                    <input type="text" class="form-control" id="nicNo" name="nic_no" placeholder="Enter NIC No" value="<?= @$nic_no ?>" style="background-color: rgb(238, 247, 236);">
                    <span class="text-danger"><?= @$messages['nic_no'] ?></span>
                  </div>

                  <!-- Gender-->
                  <div class="mb-3">
                    <label class="form-label d-block">Gender</label>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="gender" id="male" value="male" <?php if (isset($gender) && $gender == 'male') echo 'checked'; ?>>
                      <label class="form-check-label" for="male">Male</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="gender" id="female" value="female" <?php if (isset($gender) && $gender == 'female') echo 'checked'; ?>>
                      <label class="form-check-label" for="female">Female</label>
                    </div>
                    <span class="text-danger d-block"><?= @$messages['gender'] ?></span>
                  </div>

                  <!-- Address-->
                  <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" placeholder="Enter Address" style="background-color: rgb(238, 247, 236);"><?= @$address ?></textarea>
                    <span class="text-danger"><?= @$messages['address'] ?></span>
                  </div>

                  <div class="mb-3">
                    <input type="text" class="form-control" id="city" name="city" placeholder="City" value="<?= @$city ?>" style="background-color: rgb(238, 247, 236);">
                    <span class="text-danger"><?= @$messages['city'] ?></span>
                  </div>

                </div>

                <!-- Right Column -->
                <div class="col-md-6">

                  <!-- Primary Contact -->
                  <div class="mb-3">
                    <label for="contactNo1" class="form-label">Primary Contact No</label>
                    <input type="tel" class="form-control" id="contactNo1" name="contact_no1" placeholder="Enter Primary Contact Number" value="<?= @$contact_no1 ?>" style="background-color: rgb(219, 248, 212);">
                    <span class="text-danger"><?= @$messages['contact_no1'] ?></span>
                  </div>

                  <!-- Secondary Contact  -->
                  <div class="mb-3">
                    <label for="contactNo2" class="form-label">Secondary Contact No</label>
                    <input type="tel" class="form-control" id="contactNo2" name="contact_no2" placeholder="Enter Secondary Contact Number" value="<?= @$contact_no2 ?>" style="background-color: rgb(238, 247, 236);">
                  </div>

                  <!--Role    -->
                  <div class="mb-3">
                    <label for="user_role" class="form-label">Role</label>
                    <select class="form-control" id="user_role" name="user_role">
                      <option value="">Select the role</option>
                      <?php
                      $db = dbConn();
                      $sql = "SELECT * FROM user_roles WHERE Status='backend'";
                      $result = $db->query($sql);
                      if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                      ?>
                          <option value="<?= $row['Id'] ?>" <?= $row['Id'] ==  @$user_role ? 'selected' : '' ?>><?= $row['description'] ?></option>
                      <?php
                        }
                      }
                      ?>
                    </select>
                    <span class="text-danger"><?= @$messages['user_role'] ?></span>
                  </div>

                  <!--Profile Picture    -->
                  <div class="mb-3">
                    <label for="profilepic" class="form-label">Profile Picture</label>
                    <input class="form-control" type="file" id="profilepic" name="profilepic">
                    <span class="text-danger"><?= @$messages['profilepic'] ?></span>
                  </div>

                  <!-- Email-->
                  <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" value="<?= @$email ?>" style="background-color: rgb(238, 247, 236);">
                    <span class="text-danger"><?= @$messages['email'] ?></span>
                  </div>

                  <!-- Password-->
                  <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" style="background-color: rgb(238, 247, 236);">
                    <span class="text-danger"><?= @$messages['password'] ?></span>
                  </div>

                </div>
              </div> <!-- End Row -->
            </div> <!-- End Card Body -->

            <div class="card-footer text-center" style="background-color:rgb(174, 230, 176); color: black;">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </div> <!-- End Card -->
        </form>
      </div>





      <?php
      $content = ob_get_clean();
      include '../layouts.php';
      ?>