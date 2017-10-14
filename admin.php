<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 13/10/2017
 * Time: 19:22
 */
$db = new PDO('sqlite:../cart.sqlite');
$cat_query = $db->prepare("SELECT name FROM categories");
if($_GET['action']=="insert_cat") ierg4210_cat_insert();
if($_GET['action']=="update_cat") ierg4210_cat_update();
if($_GET['action']=="delete_cat") ierg4210_cat_delete();
if($_GET['action']=="insert_prod") ierg4210_prod_insert();
if($_GET['action']=="update_prod") ierg4210_prod_update();
if($_GET['action']=="delete_prod") ierg4210_prod_delete();
function ierg4210_cat_insert(){
     global $db;
     $q = $db->prepare("INSERT INTO categories (name) VALUES (?)");
     $q->execute(array($_POST['cat_name']));
}
function ierg4210_cat_update(){
    global $db;
    $q = $db->prepare("UPDATE  categories  SET name = (?) WHERE name =(?)");
    $q->execute(array($_POST['new_cat_name'],$_POST['original_cat_name']));
}
function ierg4210_cat_delete(){
    global $db;
    $q = $db->prepare("DELETE FROM  categories  WHERE name =(?)");
    $q->execute(array($_POST['cat_name_delete']));
}
function ierg4210_get_catid($catname){
    global $db;
    $q = $db->prepare("SELECT catid FROM categories WHERE name =(?)");
    $q->execute(array($catname));
    $row = $q->fetch(PDO::FETCH_ASSOC);
    return $row['catid'];

}
function ierg4210_prod_insert(){
    global $db;
   $catid = ierg4210_get_catid($_POST['insert_prod_cat_name']) ;
   $q = $db->prepare("INSERT INTO  products  (catid, name, price, description) VALUES (?,?,?,?)");
   $q->execute(array($catid,$_POST['insert_prod_name'],$_POST['insert_prod_price'],$_POST['insert_prod_description']));
   echo "file name" . $db->lastInsertId();
    upload_image('insert_file',$db->lastInsertId());
}
function ierg4210_prod_update(){
    global $db;
    $catid = ierg4210_get_catid($_POST['prod_to_update_cat_name']);
    $name = $_POST['update_prod_name'];
    $price = $_POST['update_prod_price'];
    $description=$_POST['update_prod_description'];
    if($catid || !empty($name)|| !empty($price) ||!empty($description)){
        $q = "UPDATE  products  SET ";
        $q .=(!($catid))? "catid=$catid ":"";
        $q .=(!empty($name))? "name='$name' ":"";
        $q .=(!empty($price))? "price=$price ":"";
        $q .=(!empty($description))? "description='$description',":"";
        $q .= " WHERE name = (?) ";
        $q = $db->prepare($q);
        $q->execute(array($_POST['prod_to_update_name']));
    }
    if(file_exists($_FILES['update_file']['tmp_name'] || is_uploaded_file($_FILES['update_file']['tmp_name']))){
        $q_pid = $db->prepare("SELECT pid FROM products WHERE name= (?)");
        $q_pid->execute(array($_POST['prod_to_update_name']));
        $row = $q_pid->fetch(PDO::FETCH_ASSOC);
        upload_image('update_file',$row['pid']);
    }
}
function ierg4210_prod_delete(){
    global $db;
    $q = $db->prepare("DELETE FROM  products  WHERE name =(?)");
    $q->execute(array($_POST['prod_to_delete']));
}
function upload_image($file,$id)
{
    $errors = array();
    $file_name = $_FILES[$file]['name'];
    $file_size = $_FILES[$file]['size'];
    $file_tmp = $_FILES[$file]['tmp_name'];
    $file_type = $_FILES[$file]['type'];
    $file_ext = strtolower(end(explode('.', $file_name)));
    echo 'file_ext is' . $file_ext;
    $expensions = array("jpeg", "jpg", "png");

    if (in_array($file_ext, $expensions) === false) {
        $errors[] = "extension not allowed, please choose a JPEG or PNG file.";
    }

    if ($file_size > 2097152) {
        $errors[] = 'File size must be excately 2 MB';
    }

    if (empty($errors) == true) {
        $target_path=getcwd() ."/img/" . $id. '.' . $file_ext;
        move_uploaded_file($file_tmp, $target_path);
        echo $target_path;
        echo "Success";
    } else {
        print_r($errors);
    }
}


?>
<fieldset style="display:inline-block">
<legend>Product</legend>
<form  id="prod_insert" method="POST" action="admin.php?action=insert_prod" enctype="multipart/form-data">
    ***INSERT PRODUCT***<br>
<label for="prod_catid">Category *</label>
<div><select id="prod_action" name="insert_prod_cat_name">
        <?php
        $cat_query->execute();
        while ($row = $cat_query->fetch(PDO::FETCH_ASSOC)) {
            echo '<option value="'.$row['name'].'">'.$row['name'].'</option>';
        }
?>
    </select></div>
    <label for="prod_name">Name *</label>
<div><input id="prod_name" type="text" name="insert_prod_name" required="true" pattern="^[\w\- ]+$" />
</div>
    <label for="prod_price">Price *</label>
    <div><input id="prod_price" type="text" name="insert_prod_price" required="true" pattern="^[\w\- ]+$" />
    </div>
    <label for="prod_description_text">Description *</label>
    <div><input id="prod_description" type="text" name="insert_prod_description" required="true" pattern="^[\w\- ]+$" />
    </div>
<label id ="file_label" for="prod_name">Image *</label>
<div><input id="insert_file" type="file" name="insert_file" required="true" accept="image/jpeg" /></div>
<input type="submit" value="Submit" /><br>
    * must be filled
</form>
    <form id="prod_update" method="POST" action="admin.php?action=update_prod" enctype="multipart/form-data">
        ***UPDATE PRODUCT***<br>
        <label for="prod_to_update_text">Name of product to be updated *</label>
        <div><input id="prod_to_update" type="text" name="prod_to_update_name" required="true" pattern="^[\w\- ]+$" /><br>
        <label for="prod_catid">Category </label>
        <div><select id="prod_action" name="prod_to_update_cat_name">
                <?php
                $cat_query->execute();
                while ($row = $cat_query->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="'.$row['name'].'">'.$row['name'].'</option>';
                }
                ?>
            </select></div>
        <label for="prod_name">Name </label>
        <div><input id="prod_name" type="text" name="update_prod_name"  pattern="^[\w\- ]+$" />
        </div>
            <label for="prod_price">Price *</label>
            <div><input id="prod_price" type="text" name="update_prod_price"  pattern="^[\w\- ]+$" />
            </div>
        <label for="prod_description_text">Description </label>
        <div><input id="prod_description" type="text" name="update_prod_description"  pattern="^[\w\- ]+$" />
        </div>
        <label id ="file_label" for="prod_name">Image </label>
        <div><input  type="file" name="update_file"  accept="image/*" /></div>
        <input type="submit" value="Submit" /><br>
        * must be filled
    </form>
    <form id="cat_delete" method="POST" action="admin.php?action=delete_prod" enctype="multipart/form-data">
        ***DELETE PRODUCT***<br>
        <label for="prod_to_update_text">Name of product to be deleted *</label>
        <div><input id="prod_to_delete" type="text" name="prod_to_delete" required="true" pattern="^[\w\- ]+$" /><br>
            <input type="submit" value="Submit" /><br>
            * must be filled
    </form>
</fieldset>
<fieldset style="display:inline-block">
    <legend>Categories</legend>
    <form  id="cat_insert" method="POST" action="admin.php?action=insert_cat" enctype="multipart/formdata">
        ***INSERT CATEGORY***<br>
        <label for="cat_name">Name *</label>
        <div><input id="cat_name" type="text" name="cat_name" required="true" pattern="^[\w\- ]+$" />
        </div>

        <input type="submit" value="Submit" /><br>
        * must be filled
    </form>
    <form id="cat_update" method="POST" action="admin.php?action=update_cat" enctype="multipart/formdata">
        ***UPDATE CATEGORY***<br>
        <label for="original_cat_to_update_text">Orginal Name of the category *</label>
        <div><select id="cat_to_update" name="original_cat_name">
                <?php
                $cat_query->execute();
                while ($row = $cat_query->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="'.$row['name'].'">'.$row['name'].'</option>';
                }
                ?>
            </select></div>
            <label for="cat_to_update_text">New Category Nname *</label>
            <div><input id="cat_to_update" type="text" name="new_cat_name" required="true" pattern="^[\w\- ]+$" /><br>
            <input type="submit" value="Submit" /><br>
            * must be filled
    </form>
    <form id="cat_delete" method="POST" action="admin.php?action=delete_cat" enctype="multipart/formdata">
        ***DELETE CATEGORY***<br>
        <label for="cat_to_update_text">Name of category to be deleted *</label>
        <div><input id="cat_to_delete" type="text" name="cat_name_delete" required="true" pattern="^[\w\- ]+$" /><br>
            <input type="submit" value="Submit" /><br>
            * must be filled
    </form>
</fieldset>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="js/admin.js"></script>
