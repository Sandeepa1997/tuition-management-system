<?php
ob_start();
include '../../init.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    extract($_POST);
    
    $parent_name = dataclean($parent_name ?? '');
    $parent_lname = dataclean($parent_lname ?? '');
    $email = dataclean($email ?? '');
    $password = dataclean($password ?? '');
    $contact_no_1 = dataclean($contact_no_1 ?? '');
    $nic = dataclean($nic ?? '');
    $address = dataclean($address ?? '');
    $occupation = dataclean($occupation ?? '');
    $gender = dataclean($gender ?? '');

    $messages = array();

    // Required field checks
    if (empty($parent_name)) $messages['parent_name'] = "Please enter a first name!!!";
    if (empty($parent_lname)) $messages['parent_lname'] = "Please enter a Last name!!!";
    if (empty($email)) $messages['email'] = "Please enter a email address!!!";
    if (empty($password)) $messages['password'] = "Please enter a password!!!";
    if (empty($contact_no_1)) $messages['contact_no_1'] = "Please enter a contact number!!!";
    if (empty($gender)) $messages['gender'] = "Please select your gender!!!";
    if (empty($nic)) $messages['nic'] = "Please enter a NIC number!!!";
    if (empty($address)) $messages['address'] = "Please enter a address!!!";
    if (empty($occupation)) $messages['occupation'] = "Please enter your occupation!!!";

    // Password validation
    if (!empty($password)) {
        if (strlen($password) < 8) $messages['password'] = "Password must be at least 8 characters!";
        elseif (!preg_match('/[A-Z]/', $password)) $messages['password'] = "At least one uppercase letter!";
        elseif (!preg_match('/[a-z]/', $password)) $messages['password'] = "At least one lowercase letter!";
        elseif (!preg_match('/[0-9]/', $password)) $messages['password'] = "At least one number!";
        elseif (!preg_match('/[\W_]/', $password)) $messages['password'] = "At least one special character!";
    }

    // Advanced validation
    if (!empty($parent_name) && !ctype_alpha(str_replace(' ', '', $parent_name))) {
        $messages['parent_name'] = "Only letters and spaces allowed!";
    }
    if (!empty($parent_lname) && !ctype_alpha(str_replace(' ', '', $parent_lname))) {
        $messages['parent_lname'] = "Only letters and spaces allowed!";
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
            $messages['email'] = "Email is already exists!";
        }
    }

    // NIC format and duplicate check
    if (!empty($nic)) {
        function isValidNIC($nic) {
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

        if (!isValidNIC($nic)) {
            $messages['nic'] = "Invalid NIC number format";
        } else {
            $sql = "SELECT * FROM users WHERE NIC_No = '$nic'";
            $result = $db->query($sql);
            if ($result->num_rows > 0) {
                $messages['nic'] = "NIC number already exists!";
            }
        }
    }

    // Contact No 01
    if (!empty($contact_no_1)) {
        $sql = "SELECT * FROM users WHERE Primary_Contact = '$contact_no_1'";
        $result = $db->query($sql);
        if ($result->num_rows > 0) {
            $messages['contact_no_1'] = "The Contact Number already exists!";
        }
    }

    // If no errors, insert into DB
    if (empty($messages)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $user_role = 'parent';

        $sql = "INSERT INTO users (FirstName, LastName, Email, Password, Primary_Contact, NIC_No, Gender, Address, user_role)
                VALUES ('$parent_name','$parent_lname','$email','$hashed_password','$contact_no_1','$nic','$gender','$address','$user_role')";
        $db->query($sql);

        $user_id = $db->insert_id;

        $sql = "INSERT INTO parents (Userid, Occupation) 
                VALUES ('$user_id','$occupation')";
        $db->query($sql);

        $_SESSION['NAME'] = $parent_lname;

        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo '<script>
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "You are successfully registered",
                    showConfirmButton: false,
                    timer: 3500
                }).then(function(){
                    window.location="register.php";
                });
              </script>';
    }
}
?>



<div class="container my-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-success text-white text-center">
            <h3 class="mb-0">Parent Registration</h3>
        </div>

        <div class="card-body" style="background-color: #f5fdf7;">
           <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" novalidate enctype="multipart/form-data">
    <div class="row g-4">
        <div class="col-md-6">

            <!-- First Name -->
            <label for="parent_name" class="form-label">First Name</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" name="parent_name" id="parent_name" class="form-control" placeholder="Enter First Name">
            </div>
            <span class="text-danger"><?= @$messages['parent_name'] ?></span>

            <!-- Email -->
            <label for="email" class="form-label mt-3">Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" id="email" class="form-control" placeholder="example@mail.com">
            </div>
            <span class="text-danger"><?= @$messages['email'] ?></span>

            <!-- Password -->
            <label for="password" class="form-label mt-3">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" id="password" class="form-control" placeholder="Min. 8 characters">
            </div>
            <span class="text-danger"><?= @$messages['password'] ?></span>

            <!-- NIC -->
            <label for="nic" class="form-label mt-3">NIC</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                <input type="text" name="nic" id="nic" class="form-control" placeholder="123456789V">
            </div>
            <span class="text-danger"><?= @$messages['nic'] ?></span>

            <!-- Occupation -->
            <label for="occupation" class="form-label mt-3">Occupation</label>
            <input type="text" name="occupation" id="occupation" class="form-control" placeholder="e.g., Teacher, Engineer">
            <span class="text-danger"><?= @$messages['occupation'] ?></span>

        </div>

        <div class="col-md-6">

            <!-- Last Name -->
            <label for="parent_lname" class="form-label">Last Name</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" name="parent_lname" id="parent_lname" class="form-control" placeholder="Enter Last Name">
            </div>
            <span class="text-danger"><?= @$messages['parent_lname'] ?></span>

            <!-- Gender -->
            <label class="form-label mt-3">Gender</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" id="male" value="male" <?php if (isset($gender) && $gender == 'male') echo 'checked'; ?>>
                <label class="form-check-label" for="male">Male</label>
            </div>
            <div class="form-check form-check-inline mb-2">
                <input class="form-check-input" type="radio" name="gender" id="female" value="female" <?php if (isset($gender) && $gender == 'female') echo 'checked'; ?>>
                <label class="form-check-label" for="female">Female</label>
            </div><br>
            <span class="text-danger"><?= @$messages['gender'] ?></span>

            <!-- Contact Number -->
            <label for="contact_no_1" class="form-label mt-3">Contact Number</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                <input type="tel" name="contact_no_1" id="contact_no_1" class="form-control" placeholder="07xxxxxxxx">
            </div>
            <span class="text-danger"><?= @$messages['contact_no_1'] ?></span>

            <!-- Address -->
            <label for="address" class="form-label mt-3">Address</label>
            <textarea class="form-control" name="address" id="address" rows="3" placeholder="Enter your full address"></textarea>
            <span class="text-danger"><?= @$messages['address'] ?></span>

        </div>

        <!-- Submit Button -->
        <div class="col-12 text-center mt-4">
            <button type="submit" class="btn btn-success btn-lg w-50">Submit Registration</button>
        </div>
    </div>
</form>

        </div>
    </div>
</div>


<?php
$content = ob_get_clean();
include '../layouts.php';
?>