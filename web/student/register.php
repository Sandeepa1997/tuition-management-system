<?php
ob_start();
include '../../init.php';
?>



</div>
</header>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<main class="main">

    <!-- About Section -->
    <section id="about" class="about section">

        <div class="container mt-4">

            <?php
            if ($_SERVER['REQUEST_METHOD'] == "POST") {
                extract($_POST);

                $firstname = dataclean($firstname);
                $lastname = dataclean($lastname);
                $dob = dataclean($dob);
                $address = dataclean($address);
                $city = dataclean($city);
                $contact_no1 = dataclean($contact_no1);
                $contact_no2 = dataclean($contact_no2);
                $guardian_contact1 = dataclean($guardian_contact1);
                $guardian_contact2 = dataclean($guardian_contact2);
                $nic = dataclean($nic);
                $email = dataclean($email);
                $password = dataclean($password);

                $messages = [];

                // Required field checks
                if (empty($firstname)) $messages['firstname'] = "Please enter a firstname!!!";
                if (empty($lastname)) $messages['lastname'] = "Please enter a last name!!!";
                if (empty($dob)) $messages['dob'] = "Please enter a date of birth!!!";
                if (empty($gender)) $messages['gender'] = "Please select your gender!!!";
                if (empty($address)) $messages['address'] = "Please enter an address!!!";
                if (empty($city)) $messages['city'] = "Please enter a city!!!";
                if (empty($contact_no1)) $messages['contact_no1'] = "Please enter at least one contact number!!!";
                if (empty($Grade)) $messages['Grade'] = "Please select your grade!!!";

                if (empty($email)) 
                    $messages['email'] = "Please enter an email address!!!";
                



                if (empty($guardian_contact1)) $messages['guardian_contact1'] = "Please enter guardian contact!!!";
                if (empty($password)) $messages['password'] = "Please enter a password!!!";


                // Profile pic
                $file_name = $_FILES['profilepic']['name'];
                $file_tmp = $_FILES['profilepic']['tmp_name'];
                $file_size = $_FILES['profilepic']['size'];
                $file_error = $_FILES['profilepic']['error'];

                if (!empty($file_name)) {
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'avif'];

                    if (in_array($file_ext, $allowed)) {
                        if ($file_error === 0) {
                            if ($file_size <= 2097152) {
                                $file_newName = uniqid('', true) . '.' . $file_ext;
                                $file_location = '../../system/uploads/' . $file_newName;
                                move_uploaded_file($file_tmp, $file_location);
                            } else {
                                $messages['profilepic'] = "The file is too large. Maximum size is 2MB!";
                            }
                        } else {
                            $messages['profilepic'] = "An error occurred during file upload!";
                        }
                    } else {
                        $messages['profilepic'] = "Only JPG, JPEG, PNG, GIF are allowed!";
                    }
                } else {
                    $messages['profilepic'] = "Please upload a profile picture!";
                }

                // Password validation
                if (!empty($password)) {
                    if (strlen($password) < 8) $messages['password'] = "Password must be at least 8 characters!";
                    elseif (!preg_match('/[A-Z]/', $password)) $messages['password'] = "At least one uppercase letter!";
                    elseif (!preg_match('/[a-z]/', $password)) $messages['password'] = "At least one lowercase letter!";
                    elseif (!preg_match('/[0-9]/', $password)) $messages['password'] = "At least one number!";
                    elseif (!preg_match('/[\W_]/', $password)) $messages['password'] = "At least one special character!";
                }

                // Advanced validation
                if (!empty($firstname) && !ctype_alpha(str_replace(' ', '', $firstname))) {
                    $messages['firstname'] = "Only letters and spaces allowed!";
                }
                if (!empty($lastname) && !ctype_alpha(str_replace(' ', '', $lastname))) {
                    $messages['lastname'] = "Only letters and spaces allowed!";
                }
                if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $messages['email'] = "Invalid email format!";
                }

                // Check duplicates
                $db = dbConn();
                if (!empty($email)) {
                    $sql = "SELECT * FROM users WHERE Email = '$email'";
                    $result = $db->query($sql);
                    if ($result->num_rows > 0) {
                        $messages['email'] = "Email already exists!";
                    }
                }

                if (!empty($contact_no1)) {
                    $sql = "SELECT * FROM users WHERE Primary_Contact = '$contact_no1'";
                    $result = $db->query($sql);
                    if ($result->num_rows > 0) {
                        $messages['contact_no1'] = "Primary Contact already exists!";
                    }
                }

                if (!empty($contact_no2)) {
                    $sql = "SELECT * FROM users WHERE Secondary_Contact = '$contact_no2'";
                    $result = $db->query($sql);
                    if ($result->num_rows > 0) {
                        $messages['contact_no2'] = "Secondary Contact already exists!";
                    }
                }



                if (!empty($guardian_contact1)) {
                    $sql = "SELECT * FROM users WHERE Primary_Contact = '$guardian_contact1'";
                    $result = $db->query($sql);
                    if ($result->num_rows == 0) {
                        $messages['guardian_contact1'] = " No guradian contact found!";
                    }
                }


                if (!empty($guardian_contact2)) {
                    $sql = "SELECT * FROM users WHERE Secondary_Contact = '$guardian_contact2'";
                    $result = $db->query($sql);
                    if ($result->num_rows > 0) {
                        $messages['guardian_contact2'] = "No guradian contact found!!";
                    }
                }


                // Date of Birth validation
                if (!empty($dob)) {
                    $birthDate = new DateTime($dob);
                    $today = new DateTime();
                    $age = $birthDate->diff($today)->y;

                    if ($birthDate > $today) {
                        $messages['dob'] = "Birthday cannot be today or a future date!";
                    } elseif ($age < 13) {
                        $messages['dob'] = "Student must be at least 13 years old!";
                    }
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


                if (!empty($nic)) {
                    if (!empty($nic)) {
                        $sql = "SELECT NIC_No, p.Id AS parent_id
            FROM users u
            INNER JOIN parents p ON u.Id = p.Userid
            WHERE u.NIC_No = '$nic'";
                        $result = $db->query($sql);

                        if ($result->num_rows == 0) {
                            $messages['nic'] = "No parent found";
                        } else {
                            $row = $result->fetch_assoc();
                            $parentId = $row['parent_id'];
                        }
                    }
                }






                // If no errors, insert into DB
                ($messages);
                if (empty($messages)) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $user_role = 'Student';

  // Insert into users table
 $sql = "INSERT INTO users (FirstName, LastName, Gender, Address, City, Primary_Contact, Secondary_Contact, Email, Password, user_role,
  Profile_Picture)
VALUES
    ('$firstname','$lastname','$gender','$address','$city','$contact_no1','$contact_no2','$email','$hashed_password','$user_role','$file_newName')";
             $db->query($sql);
             $user_id = $db->insert_id;

                    // Insert into students table
                    $sql = "INSERT INTO students (Userid, dob, guardian_contact1, guardian_contact2, grade_levels_id,guardian_id) 
            VALUES ('$user_id', '$dob', '$guardian_contact1', '$guardian_contact2','$Grade','$parentId')";
                    $db->query($sql);

                    $student_id = $db->insert_id;

                    // Generate short year
                    $short_year = date('y');

                    // Generate random 3-digit number
                    $random_number = rand(100, 999);

                    // Create registration number
                    $reg_number = "R" . $short_year . $Grade . $random_number;

                    // Update students table
                    $sql = "UPDATE students SET reg_no = '$reg_number' WHERE Id = '$student_id'";
                    $db->query($sql);

                    $_SESSION['NAME'] = $lastname;

                    $_SESSION['reg_no'] = $reg_number;

                    header("Location: success.php");

                    exit();
                }
            }
            ?>

            <div class="card">
                <div class="card-header bg-success text-white "><i class="bi bi-pencil"></i>
                    <h6 class="bg-success text-white">SCIENCEMORE</h6>
                </div>

                <div class="card-body" style="background-color:rgb(174, 230, 176); color: black;">

                    <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" novalidate enctype="multipart/form-data">

                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <!-- First Name -->
                                <div class="form-group mb-3">
                                    <label for="firstName" class="fw-bold">First Name</label>
                                    <input type="text" class="form-control" style="background-color: rgb(222, 236, 219);" id="firstName" placeholder="Enter First Name" style="font-size: 14px;" name="firstname" value="<?= @$firstname ?>">
                                    <span class="text-danger" style="font-size: 13px;"><?= @$messages['firstname'] ?></span>
                                </div>

                                <!-- Last Name -->
                                <div class="form-group mb-3">
                                    <label for="lastName" class="fw-bold">Last Name</label>
                                    <input type="text" class="form-control" style="background-color: rgb(222, 236, 219);" id="lastName" placeholder="Enter Last Name" style="font-size: 14px;" name="lastname" value="<?= @$lastname ?>">
                                    <span class="text-danger" style="font-size: 13px;"><?= @$messages['lastname'] ?></span>
                                </div>

                                <!-- Date of Birth -->
                                <div class="form-group mb-3">
                                    <label for="dob" class="fw-bold">Date of Birth</label>
                                    <input type="date" class="form-control" style="background-color: rgb(222, 236, 219);" id="dob" name="dob" style="font-size: 14px;">
                                    <span class="text-danger" style="font-size: 13px;"><?= @$messages['dob'] ?></span>
                                </div>

                                <!-- Gender -->
                                <div class="form-group mb-3">
                                    <label class="fw-bold">Gender</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" style="background-color: rgb(222, 236, 219);" type="radio" name="gender" id="male" value="male" <?php if (isset($gender) && $gender == 'male') {
                                                                                                                                                                            echo 'checked';
                                                                                                                                                                        } ?>>
                                        <label class="form-check-label" for="male">Male</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" style="background-color: rgb(222, 236, 219);" type="radio" name="gender" id="female" value="female" <?php if (isset($gender) && $gender == 'female') {
                                                                                                                                                                                echo 'checked';
                                                                                                                                                                            } ?>>
                                        <label class="form-check-label" for="female">Female</label>
                                    </div>
                                    <span class="text-danger" style="font-size: 13px;"><?= @$messages['gender'] ?></span>
                                </div>


                                <!-- Address -->
                                <div class="form-group mb-3">
                                    <label for="addressLine1" class="fw-bold">Address</label>
                                    <textarea class="form-control" style="background-color: rgb(222, 236, 219);" id="address" placeholder="Enter Address" style="font-size: 14px;" name="address"><?= @$address ?></textarea>
                                    <span class="text-danger" style="font-size: 13px;"><?= @$messages['address'] ?></span>
                                    <input type="text" class="form-control mt-2" style="background-color: rgb(222, 236, 219);" id="city" placeholder="City" style="font-size: 14px;" name="city">
                                    <span class="text-danger" style="font-size: 13px;"><?= @$messages['city'] ?></span>
                                </div>

                                <!-- Contact Number -->
                                <div class="form-group mb-3">
                                    <label for="contactNo1" class="fw-bold">Contact No</label>
                                    <input type="tel" class="form-control mb-2" style="background-color: rgb(222, 236, 219);" id="contactNo1" placeholder="Primary Contact No" style="font-size: 14px;" name="contact_no1">
                                    <input type="tel" class="form-control" style="background-color: rgb(222, 236, 219);" id="contactNo2" placeholder="Secondary Contact No" style="font-size: 14px;" name="contact_no2">
                                    <span class="text-danger" style="font-size: 13px;"><?= @$messages['contact_no1'] ?></span>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <!-- grade -->
                                <div class="form-group">
                                    <label for="Grade" class="fw-bold">Grade</label>
                                    <select name="Grade" id="Grade" class="form-control" style="background-color: rgb(222, 236, 219);">
                                        <option value="">--</option>
                                        <?php

                                        $db = dbConn();
                                        $sql = "SELECT * FROM grade_levels";
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
                                    <span class="text-danger" style="font-size: 13px;"><?= @$messages['Grade'] ?></span>

                                </div>

                                <!-- Guardian Contact -->
                                <div class="form-group mt-3">
                                    <label for="guardianContact1" class="fw-bold">Guardian's Contact</label>
                                    <input type="tel" class="form-control mb-2" style="background-color: rgb(222, 236, 219);" id="guardianContact1" placeholder="Primary Guardian Contact" style="font-size: 14px;" name="guardian_contact1">
                                    <input type="tel" class="form-control" style="background-color: rgb(222, 236, 219);" id="guardianContact2" placeholder="Secondary Guardian Contact" style="font-size: 14px;" name="guardian_contact2">
                                    <span class="text-danger" style="font-size: 13px;"><?= @$messages['guardian_contact1'] ?></span>
                                    <span class="text-danger" style="font-size: 13px;"><?= @$messages['guardian_contact2'] ?></span>
                                </div>

                                <!--  Guardian NIC  -->
                                <div class="form-group mt-3">
                                    <label for="NIC" class="fw-bold">Guardian NIC No</label>
                                    <input type="text" class="form-control" style="background-color: rgb(222, 236, 219);" id="lastName" placeholder="Enter guardian's NIC" style="font-size: 14px;" name="nic" value="<?= @$nic ?>">
                                    <span class="text-danger" style="font-size: 13px;"><?= @$messages['nic'] ?></span>
                                </div>

                                <!-- Email -->
                                <div class="form-group mt-3">
                                    <label for="email" class="fw-bold">Email Address</label>
                                    <input type="email" class="form-control" style="background-color: rgb(222, 236, 219);" id="email" placeholder="Enter Email" style="font-size:14px;" name="email" value="<?= @$email ?>" style="background-color: rgb(222, 236, 219);">
                                    <span class="text-danger" style="font-size: 13px;"><?= @$messages['email'] ?></span>
                                </div>

                                <!-- Password -->
                                <div class="form-group mt-3">
                                    <label for="password" class="fw-bold">Password</label>
                                    <input type="password" class="form-control" style="background-color: rgb(222, 236, 219);" id="password" placeholder="Enter Password" style="font-size: 14px;" name="password">
                                    <span class="text-danger" style="font-size: 13px;"><?= @$messages['password'] ?></span>
                                </div>

                                <!--Profile-Picture -->
                                <div class="mt-3">
                                    <label for="formFile" class="form-label fw-bold">Profile Picture</label>
                                    <input class="form-control" type="file" id="profilepic" name="profilepic" style="background-color: rgb(222, 236, 219);">

                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </section>
    <!-- /About Section -->

    <?php
    $content = ob_get_clean();
    include '../layouts.php';
    ?>