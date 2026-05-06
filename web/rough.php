  <!--appointment booking cde-->
  <!-- Search Start -->
  <div class="container-fluid bg-success mt-5 mb-5 wow fadeIn" data-wow-delay="0.2s" data-aos="fade-up" style="padding: 35px;">
    <div class="container">
      <div class="col-md-6 mb-3 bg-dark border-0 w-50 py-2 rounded text-center" data-aos="fade-up">
        <h2 class="text-white fw-bold m=0">Book Your Appointment!</h2>
      </div>
<form action="appointment/check_availability.php" method="post">
  <div class="row g-3">

    <!-- Appointment Date -->
    <div class="col-md-4">
      <input type="date" name="date" required class="form-control border-0 py-3" />
    </div>

    <!-- Reason for Appointment -->
    <div class="col-md-4">
      <input type="text" name="reason" class="form-control border-0 py-3" placeholder="Reason (e.g. Progress discussion)" required />
    </div>

    <!-- Select Teacher -->
    <div class="col-md-4">
      <select name="teacher_id" class="form-select border-0 py-3" required>
        <option value="">-- Select Teacher --</option>
        <?php
        $db = dbConn();
        $sql = "SELECT Id, FirstName, LastName FROM teachers WHERE Status = 1";
        $result = $db->query($sql);
        while ($row = $result->fetch_assoc()) {
          echo "<option value='{$row['Id']}'>{$row['FirstName']} {$row['LastName']}</option>";
        }
        ?>
      </select>
    </div>

    <!-- Submit -->
    <div class="col-md-12 text-end">
      <button type="submit" name="action" value="check_date" class="btn btn-dark w-100 py-3">Check Availability</button>
    </div>

  </div>
</form>

    </div>
  </div>
  <!-- Search End -->