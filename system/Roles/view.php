<?php
ob_start();
include '../../init.php';
 ?>

<div class="row">
  <div class="col-md-4">
  <div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">Enter User Role</h3>
         </div>
<?php

  if($_SERVER['REQUEST_METHOD']=="POST"){
    extract($_POST);
    
    $role=dataclean($role);

   $messages = array();
   //Form required fields validation
   if(empty($role)){
    $messages['role']="Please enter a Role!!!";
   }

   if(empty($messages)){
         $db = dbConn();
         $sql = "INSERT INTO roles(Role_Name) VALUES ('$role')";
         $db->query($sql);

   }
  
  }
   
?>


 <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF'])?>" novalidate>
 <div class="card-body"  style="background-color:rgb(174, 230, 176);" >

 <div class="form-group">
      <label for="role">Role</label>
      <input type="text" class="form-control" style="background-color: rgb(233, 241, 233);" id="role" placeholder="Enter the role" name="role" value="<?= @$role?>">
      <span class="text-danger"><?= @$messages['role']?></span>
    </div>

</div>

<div class="card-footer"  style="background-color:rgb(174, 230, 176);" >
    <button type="submit" class="btn btn-primary">Submit</button>
  </div>
</form>

</div>
</div>
<div class="col-md-8">

<?php

 $db=dbConn();
 $sql="SELECT * FROM roles";
 $result=$db->query($sql);

?>

    <table class="table table-striped">
       <thead class="bg-success">
          <tr>
            <th>Id</th>
            <th>Role</th>

          </tr>
       </thead>
<tbody style="background-color: rgb(174, 230, 176);">

    <?php
      if($result->num_rows>0){
       while( $row=$result->fetch_assoc()){
    ?>

   <tr>
      <td><?=$row['Id'] ?></td>
      <td><?=$row['Role_Name'] ?></td>
    </tr>
    
    <?php
       }
      }
      ?>

</tbody>
</table>

</div>
</div>



<?php
$content= ob_get_clean();
include '../layouts.php';
 ?>