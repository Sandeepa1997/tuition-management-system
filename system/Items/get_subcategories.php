<?php
include '../../init.php';
extract($_POST);


$db=dbConn();

$sql="SELECT * FROM subcategories WHERE category_id ='$cat_id' ";
$result = $db->query($sql);

?>
<option value="">--</option>
<?php
if($result->num_rows>0){
    while($row=$result->fetch_assoc()){
        ?>
<option value="<?=$row['id']?>"><?=$row['name']?></option>
        <?php
    }
}
?>