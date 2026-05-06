<?php
ob_start();
include '../../init.php';
?>


</div>
</header>

<head>
    <style>
        #experience tbody {
            background-color: red !important;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</head>

<?php


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    extract($_POST);

    $firstname = dataclean($firstname);
    $lastname = dataclean($lastname);
    $nicNo = dataclean($nic_no);
    $address = dataclean($address);
    $Email = dataclean($email);
    $contact_no1 = dataclean($contact_no1);
    $contact_no2 = dataclean($contact_no2);
    $ALstream = dataclean($ALstream);
    $results = dataclean($results);
    //$university_name = dataclean($university_name);
    //$degree_name = dataclean($degree_name);
    // $degree_class = dataclean($degree_class);
    $password = dataclean($password);



    $messages = array();

    //Form required field validation

    //Title
    if (empty($title)) {
        $messages['title'] = "Please select your title!...";
    }


    //First Name
    if (empty($firstname)) {
        $messages['firstname'] = "The First Name should not be blanked!...";
    }

    //Last Name  
    if (empty($lastname)) {
        $messages['lastname'] = "The Last Name should not be blanked!...";
    }

    //Date of Birth
    if (empty($dob)) {
        $messages['dob'] = "Please select a birth date!...";
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

    //degree 
    if (empty($degree)) {
        $messages['degree'] = "Please select if you have a degree.";
    }

    //Email     
    if (empty($email)) {
        $messages['email'] = "The Email should not be blanked!...";
    }

    //Contact No         
    if (empty($contact_no1)) {
        $messages['contact_no1'] = "Please enter at least one contact number!...";
    }



    //Qualifications
    if (empty($ALstream)) {
        $messages['ALstream'] = "Please enter a A/L stream!...";
    }

    if (empty($results)) {
        $messages['results'] = "Enter your A/L results!...";
    }


    //Password      
    if (empty($password)) {
        $messages['password'] = "The password should not be blanked!...";
    }

    //subject      
    if (empty($subject)) {
        $messages['subject'] = "please select your subject!...";
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

    //Advanced Validation//

    //First Name Field
    if (!empty($firstname)) {
        if (ctype_alpha(str_replace(' ', '', $firstname)) === false) {
            $messages['firstname'] = "Only letters and white space allowed";
        }
    }

    //Last Name field
    if (!empty($lastname)) {
        if (ctype_alpha(str_replace(' ', '', $lastname)) === false) {
            $messages['lastname'] = "Only letters and white space allowed";
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
    }


    //NIC length validation
    function isValidNIC($nic_no)
    {

        $len = strlen($nic_no);

        if ($len === 10) {
            $digits = substr($nic_no, 0, 9);
            $lastChar = strtoupper($nic_no[9]);
            return ctype_digit($digits) && ($lastChar === 'V' || $lastChar === 'X');
        } elseif ($len === 12) {
            return ctype_digit($nic_no);
        }
        return false;
    }

    $NIC = isValidNIC($nic_no);

    if ($NIC == false) {
        $messages['nic_no'] = "Invalid NIC number format";
    }


    //Check already exist in the database 
    //NIC
    if (!empty($nic_no)) {
        $db = dbConn();
        $sql = "SELECT * FROM users WHERE NIC_No ='$nic_no'";
        $result = $db->query($sql);

        if ($result->num_rows > 0) {
            $messages['nic_no'] = "The NIC is already exsit!!!";
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


    //File upload
    if (empty($messages)) {
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
                        $file_location = '../../system/uploads/' . $file_newName;
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
    }


    //Insert into database//
    if (empty($messages)) {

        $password = password_hash($password, PASSWORD_DEFAULT);

        $user_role = 'Teacher';

        $db = dbConn();

        $sql = "INSERT INTO users (
              FirstName, LastName, NIC_No, Gender, Address, City, Email,Password,
              Primary_Contact, Secondary_Contact, user_role,  
              Profile_Picture
          ) 
          VALUES (
              '$firstname', '$lastname', '$nic_no', '$gender', '$address', '$city', '$email',
              '$password','$contact_no1', '$contact_no2', '$user_role','$file_newName'
              
          )";

        $db->query($sql);

        $userid = $db->insert_id;

        $sql = "INSERT INTO teachers (
             userid,title,FirstName,LastName,Dob,Gender,Primary_Contact, Secondary_Contact,Address,City,AL_stream,AL_results,
             degree,Email,Password,NIC_No,subject_id,Profile_Picture
               
               )
                 

          VALUES (
              '$userid','$title','$firstname', '$lastname','$dob','$gender','$contact_no1','$contact_no2','$address','$city','$ALstream','$results',
              '$degree','$email','$password','$nic_no','$subject','$file_newName')";

        $db->query($sql);

        $application_id = $db->insert_id;

        $i = 0;
        foreach ($scl_ins_name as $val) {

            $scl_ins_type = $type[$i];
            $duration = $duration[$i];
            $sql = "INSERT INTO teacher_experience (application_id,School_Institute_name,type,duration)
            VALUES('$application_id','$val','$scl_ins_type','$duration')";
            $db->query($sql);


            $i++;
        }


        $i = 0;
        foreach ($university_name as $val) {

            $degree_name = $degree_name[$i];
            $degree_class = $degree_class[$i];
            $sql = "INSERT INTO teacher_degrees (application_id,university_name,degree_name,degree_class)
                VALUES('$application_id','$val','$degree_name','$degree_class')";
            $db->query($sql);


            $i++;
        }


        if (!empty($grades)) {
            foreach ($grades as $grade) {

                $sql = "INSERT INTO teacher_grades (application_id, grade_level_id) 
                        VALUES ('$application_id', '$grade')";
                $db->query($sql);
            }
        }


        echo '<script>
        Swal.fire({
  position: "center",
  icon: "success",
  title: "Your application is successfully submitted",
  showConfirmButton: false,
  timer: 3500
  }).then(function(){window.location="register.php"});
  </script>';
    }
}
?>



<main class="main">


    <h1 class="display-4 fw-bold text-success text-center mb-3  ">
        <span class="fade-in-up d-block"> "Start your teaching career</span>
        <span class="fade-in-up d-block">with us"</span>
    </h1>

    <!-- About Section -->
    <section id="about" class="about section">
        <div class="container mt-4">
            <div class="d-flex align-items-start gap-4">


                <!-- Form Column  -->
                <div class="card my-0.5" style="width:100%;">
                    <div class="card-header bg-success text-white">
                        <h6 class="bg-success text-white m-0  text-start d-block">SCIENCEMORE</h6>
                    </div>

                    <div class="card-body" style="background-color:rgb(174, 230, 176); color:black;">
                        <form method="post" id="formspec" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" novalidate enctype="multipart/form-data">

                            <div class="row gx-5">
                                <!-- Left Column -->
                                <div class="col-md-6">


                                    <!-- Title -->
                                    <div class="form-group mb-4 text-start d-block">
                                        <label for="exampleFormControlSelect1" class="fw-bold form-control-sm">Title</label>
                                        <select class="form-control" id="exampleFormControlSelect1" style="background-color: rgb(222, 236, 219);" name="title">
                                            <option value="">--</option>
                                            <option>Mr</option>
                                            <option>Mrs</option>
                                            <option>Miss</option>
                                        </select>
                                        <span class="text-danger" style="font-size: 13px;"><?= @$messages['title'] ?></span>
                                    </div>

                                    <!-- First Name -->
                                    <div class="form-group mb-3">
                                        <label for="firstName" class="fw-bold form-control-sm text-start d-block">First Name</label>
                                        <input type="text" class="form-control" style="background-color: rgb(222, 236, 219) ;" id="firstName" placeholder="Enter First Name" name="firstname" value="<?= @$firstname ?>">
                                        <span class="text-danger" style="font-size: 13px;"><?= @$messages['firstname'] ?></span>
                                    </div>

                                    <!-- Last Name -->
                                    <div class="form-group mb-3">
                                        <label for="lastName" class="fw-bold form-control-sm text-start d-block">Last Name</label>
                                        <input type="text" class="form-control" style="background-color: rgb(222, 236, 219);" id="lastName" placeholder="Enter Last Name" name="lastname" value="<?= @$lastname ?>">
                                        <span class="text-danger" style="font-size: 13px;"><?= @$messages['lastname'] ?></span>
                                    </div>

                                    <!-- Date of Birth -->
                                    <div class="form-group mb-3">
                                        <label for="dob" class="fw-bold form-control-sm text-start d-block">Date of Birth</label>
                                        <input type="date" class="form-control" style="background-color: rgb(222, 236, 219);" id="dob" name="dob">
                                        <span class="text-danger" style="font-size: 13px;"><?= @$messages['dob'] ?></span>
                                    </div>

                                    <!-- Gender -->
                                    <div class="form-group mb-3">
                                        <label class="fw-bold form-control-sm text-start d-block">Gender</label><br>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" style="background-color: rgb(222, 236, 219);" type="radio" name="gender" id="male" value="male" <?= (isset($gender) && $gender == 'male') ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="male">Male</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" style="background-color: rgb(222, 236, 219);" type="radio" name="gender" id="female" value="female" <?= (isset($gender) && $gender == 'female') ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="female">Female</label>
                                        </div>
                                    </div>
                                    <span class="text-danger" style="font-size: 13px;"><?= @$messages['gender'] ?></span>

                                    <!-- Contact Number -->
                                    <div class="form-group mb-3">
                                        <label for="contactNo1" class="fw-bold form-control-sm text-start d-block">Contact No</label>
                                        <input type="tel" class="form-control mb-2" style="background-color: rgb(222, 236, 219);" id="contactNo1" placeholder="Primary Contact No" name="contact_no1" value="<?= @$contact_no1 ?>">
                                        <input type="tel" class="form-control" style="background-color: rgb(222, 236, 219);" id="contactNo2" placeholder="Secondary Contact No" name="contact_no2" value="<?= @$contact_no2 ?>">
                                        <span class="text-danger" style="font-size: 13px;"><?= @$messages['contact_no1'] ?></span>
                                    </div>

                                    <!-- Address -->
                                    <div class="form-group mt-3">
                                        <label for="address" class="fw-bold form-control-sm text-start d-block">Address</label>
                                        <textarea class="form-control" style="background-color: rgb(222, 236, 219);" id="address" placeholder="Enter Address" name="address"><?= @$address ?></textarea>
                                        <span class="text-danger" style="font-size: 13px;"><?= @$messages['address'] ?></span>
                                    </div>

                                    <!-- City -->
                                    <div class="form-group mt-3">
                                        <input type="text" class="form-control form-control-sm" style="background-color: rgb(222, 236, 219);" id="city" placeholder="City" name="city" value="<?= @$city ?>">
                                        <span class="text-danger" style="font-size: 13px;"><?= @$messages['city'] ?></span>
                                    </div>



                                    <!-- Experience -->
                                    <div class="form-group mt-4">
                                        <label for="experience" class="fw-bold form-control-sm text-start d-block">Experience</label>
                                        <small class="text-muted fst-italic">
                                            hint: Enter each previous (or current) workplace, choose whether it’s a
                                            government or private institution, and state the period you worked there.
                                        </small>


                                        <!-- School/Institute Name -->


                                        <table class="table table-stripped" id="experience">
                                            <thead>
                                                <tr>
                                                    <th>School/Institute name</th>
                                                    <th>Type </th>
                                                    <th>duration</th>
                                                </tr>

                                            </thead>
                                            <tbody id="experience">
                                                <tr>
                                                    <td><input type="text" id="scl_ins_name" name="scl_ins_name[]" class="form-control"></td>
                                                    <td><select name="type[]" id="type" class="form-control">
                                                            <option value="">--</option>
                                                            <option value="Government">Government</option>
                                                            <option value="Private">Private</option>

                                                        </select>
                                                    </td>

                                                    <td><input type="text" id="" name="duration[]" class="form-control"></td>



                                                </tr>
                                            </tbody>
                                        </table>


                                        <button type="button" class="btn btn-success btn-sm mt-3" id="addrow">Add More</button>
                                    </div>
                                </div>


                                <!-- Right Column -->
                                <div class="col-md-6">

                                    <!-- Qualifications -->
                                    <div class="form-group">
                                        <label for="qualifications" class="fw-bold form-control-sm text-start d-block">Qualifications</label>

                                        <!-- A/L qualifications -->
                                        <div class="row g-2">
                                            <!-- A/L stream -->
                                            <div class="col-md-6  mt-2 ">
                                                <input type="text" class="form-control" id="A/L_stream" name="ALstream" placeholder="Enter your A/L stream" style="background-color: rgb(222, 236, 219);" value="<?= @$ALstream ?>">
                                                <span class="text-danger" style="font-size: 13px;"><?= @$messages['ALstream'] ?></span>
                                            </div>

                                            <!-- A/L results -->
                                            <div class="col-md-6 mt-2">
                                                <input type="text" class="form-control" id="results" name="results" placeholder="Enter your A/L results" style="background-color: rgb(222, 236, 219);" value="<?= @$results ?>">
                                                <span class="text-danger" style="font-size: 13px;"><?= @$messages['results'] ?></span>
                                            </div>
                                        </div>

                                        <!-- Degree qualification -->
                                        <label class="fw-bold form-control-sm text-start d-block mt-4 ">Do you have a degree?</label><br>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" style="background-color: rgb(222, 236, 219);" type="radio" name="degree" id="degree" value="Yes" <?= (isset($Yes) && $degree == 'Yes') ? 'checked' : '' ?> onchange="showUniversity(this.value)">
                                            <label class="form-check-label" for="yes">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" style="background-color: rgb(222, 236, 219);" type="radio" name="degree" id="nodegree" value="No" <?= (isset($No) && $nodegree == 'No') ? 'checked' : '' ?> onchange="showUniversity(this.value)">
                                          
                                            <label class="form-check-label" for="no">No</label>
                                        </div>




                                        <!-- university Name -->
                                        <div class="row g-2 mt-3" id="uni">
                                            <div class="col-12">
                                                <label class="form-label fw-bold fs-6">University Information</label>
                                                <div id="university-section">
                                                    <div class="row university-group mb-2">
                                                        <div class="col-md-4">
                                                            <label for="" class="fw-bold form-control-sm ">University Name</label>
                                                            <input type="text" name="university_name[]" class="form-control" placeholder="Enter your university">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="" class="fw-bold form-control-sm "> Degree Name</label>
                                                            <input type="text" name="degree_name[]" class="form-control" placeholder="Enter your degree name">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="" class="fw-bold form-control-sm "> Degree Class</label>
                                                            <input type="text" name="degree_class[]" class="form-control" placeholder="Enter class of the degree">
                                                        </div>

                                                    </div>
                                                </div>

                                                <button type="button" class="btn btn-success btn-sm mt-2" id="add-university">Add More</button>
                                            </div>
                                        </div>



                                        <!-- Email -->
                                        <div class="form-group mt-3">
                                            <label for="email" class="fw-bold form-control-sm text-start d-block">Email Address</label>
                                            <input type="email" class="form-control" style="background-color: rgb(222, 236, 219);" id="email" placeholder="Enter Email" name="email" value="<?= @$email ?>">
                                            <span class="text-danger" style="font-size: 13px;"><?= @$messages['email'] ?></span>
                                        </div>

                                        <!-- Password -->
                                        <div class="form-group mt-3">
                                            <label for="password" class="fw-bold form-control-sm text-start d-block">Password</label>
                                            <input type="password" class="form-control" style="background-color: rgb(222, 236, 219);" id="password" placeholder="Enter Password" name="password">
                                            <span class="text-danger" style="font-size: 13px;"><?= @$messages['password'] ?></span>
                                        </div>

                                        <!-- NIC No -->
                                        <div class="form-group mt-3">
                                            <label for="nicNo" class="fw-bold form-control-sm text-start d-block">NIC No</label>
                                            <input type="text" class="form-control" id="nicNo" placeholder="Enter NIC No" name="nic_no" value="<?= @$nic_no ?>" style="background-color: rgb(238, 247, 236);">
                                            <span class="text-danger" style="font-size: 13px;"><?= @$messages['nic_no'] ?></span>
                                        </div>



                                        <!-- Subjects -->
                                        <div class="form-group mt-4 text-start d-block">
                                            <label for="subject" class="fw-bold form-control-sm">Select Your Subject</label>
                                            <select class="form-control" id="subject" name="subject" style="background-color: rgb(222, 236, 219);">
                                                <option value="">--</option>
                                                <?php
                                                $db = dbConn();
                                                $sql = "SELECT * FROM subjects";
                                                $result = $db->query($sql);
                                                while ($row = $result->fetch_assoc()):
                                                    $selected = (isset($subject) && $subject == $row['id']) ? 'selected' : '';
                                                ?>
                                                    <option value="<?= $row['id'] ?>" <?= $selected ?>><?= $row['name'] ?></option>
                                                <?php endwhile; ?>
                                            </select>
                                            <span class="text-danger" style="font-size: 13px;"><?= @$messages['subject'] ?></span>
                                        </div>


                                        <!--Profile-Picture -->
                                        <div class="mt-3">
                                            <label for="formFile" class="form-label fw-bold">Profile Picture</label>
                                            <input class="form-control" type="file" id="profilepic" name="profilepic" style="background-color: rgb(222, 236, 219);">

                                        </div>
                                        <span class="text-danger" style="font-size: 13px;"><?= @$messages['profilepic'] ?></span>


                                        <!-- Preffered Grades -->
                                        <div class="form-group mt-3">
                                            <label class="fw-bold">Select Grades</label><br>
                                            <?php
                                            $db = dbConn();
                                            $sql = "SELECT id, name FROM grade_levels";
                                            $result = $db->query($sql);
                                            while ($row = $result->fetch_assoc()) {
                                                $checked = (isset($grades) && in_array($row['id'], $grades)) ? 'checked' : '';
                                                echo '
                                                    <div class="form-check form-check-inline">
                                                           <input class="form-check-input" type="checkbox" name="grades[]" value="' . $row['id'] . '" id="grade_' . $row['id'] . '" ' . $checked . '>
                                                              <label class="form-check-label" for="grade_' . $row['id'] . '">' . $row['name'] . '</label>
                                                                       </div>
                                                                             ';
                                            }
                                            ?>
                                            <br>

                                        </div>

                                        <!-- Date -->
                                        <div class="form-group mt-3">
                                            <label for="date" class="fw-bold form-control-sm text-start d-block">Date</label>
                                            <input type="date" class="form-control" id="date" placeholder="Enter date" name="date" value="<?= @$date ?>" style="background-color: rgb(238, 247, 236);">
                                            <span class="text-danger" style="font-size: 13px;"><?= @$messages['date'] ?></span>
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
        </div>
    </section>

    <!-- /About Section -->
    <script>
        //add more rows//
        document.addEventListener('DOMContentLoaded', function() {
            const tableBody = document.querySelector('#experience tbody');
            const addRowBtn = document.getElementById('addrow');

            addRowBtn.addEventListener('click', () => {
                const newRow = document.createElement('tr');
                newRow.innerHTML = '<td><input type="text" id="" name="scl_ins_name[]" class="form-control"></td><td><select id="type" class="form-control" name="type[]"><option value="">--</option> <option value="">Government</option> <option value="">Private</option></select></td> <td><input type="text" id="" name="duration[]" class="form-control"></td><td><button type="button" class="btn btn-danger btn-sm removerow">Remove</button></td>';

                tableBody.appendChild(newRow);

            });

            //remove rows
            tableBody.addEventListener('click', (event) => {
                if (event.target && event.target.classList.contains('removerow')) {
                    const row = event.target.closest('tr');

                    if (tableBody.rows.length > 1) {
                        row.remove();
                    }
                }
            })
        })


        document.addEventListener('DOMContentLoaded', function() {
            const universitySection = document.getElementById('university-section');
            const addUniversityBtn = document.getElementById('add-university');

            addUniversityBtn.addEventListener('click', () => {
                const newGroup = document.createElement('div');
                newGroup.className = 'row university-group mb-2';
                newGroup.innerHTML = `
        <div class="col-md-4">
          <input type="text" name="university_name[]" class="form-control" placeholder="Enter your university">
        </div>
        <div class="col-md-4">
          <input type="text" name="degree_name[]" class="form-control" placeholder="Enter your degree name">
        </div>
        <div class="col-md-4">
          <input type="text" name="degree_class[]" class="form-control" placeholder="Enter class of the degree">
        </div>
        <div class="col-md-4 mt-2 d-flex align-items-center">
          <button type="button" class="btn btn-danger btn-sm remove-university">Remove</button>
        </div>
      `;
                universitySection.appendChild(newGroup);
            });

            universitySection.addEventListener('click', (event) => {
                if (event.target && event.target.classList.contains('remove-university')) {
                    const group = event.target.closest('.university-group');
                    if (document.querySelectorAll('.university-group').length > 1) {
                        group.remove();
                    }
                }
            });
        });


        function handleDegreeSelection() {
            const degreeYes = document.querySelector('input[name="degree"][value="Yes"]');
            const degreeNo = document.querySelector('input[name="degree"][value="No"]');
            const degreeFields = document.querySelectorAll('input[name="university_name[]"], input[name="degree_name[]"], input[name="degree_class[]"]');

            if (degreeNo.checked) {
                degreeFields.forEach(field => {
                    field.value = "";
                    field.disabled = true;
                });
            } else {
                degreeFields.forEach(field => {
                    field.disabled = false;
                });
            }
        }

        function showUniversity(v) {
            alert(v);
            var div = document.getElementById("uni");
            if (v=='Yes') {
                div.style.display = "block";
            } else {
                div.style.display = "none";
            }

        }
    </script>

    <?php
    $content = ob_get_clean();
    include '../layouts.php';
    ?>