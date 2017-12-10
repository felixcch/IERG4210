
<?php
session_start();
include_once('lib/util.php');
$auth = ierg4210_validateCookie();
if(!$auth) {header("Location:login.php");exit();}
else{
    echo $auth['isAdmin'];
    if($auth['isAdmin']==0) {
        header("Location:index.php");
        exit();
    }
}
echo "You are logged as : ". $auth['em'];
?>

<html>
<head>
	<meta charset="utf-8" />
	<title>IERG4210 Shop - Admin Panel</title>
	<link href="incl/admin.css" rel="stylesheet" type="text/css"/>
</head>

<body>
<h1>IERG4210 Shop - Admin Panel <a href="index.php">Go to main page now</a>  <a href="login.php">Back to login php</a> <a href="admin.php">Refresh</a></h1>

<article id="main">

<section id="categoryPanel">
	<fieldset>
		<legend>New Category</legend>
		<form id="cat_insert" method="POST" action="lib/admin-process.php?action=<?php echo($action='cat_insert');?>" onsubmit="return false;">
			<label for="cat_insert_name">Name</label>
			<div><input id="cat_insert_name" type="text" name="name" required="true" pattern="^[\w\- ]+$" /></div>
            <input type="hidden" name ="cat_insert_nonce" value="<?php echo ierg4210_csrf_getNonce($action);?>"/>
			<input type="submit" value="Submit" />
		</form>
	</fieldset>
	
	<!-- Generate the existing categories here -->
	<ul id="categoryList"></ul>
</section>

<section id="categoryEditPanel" class="hide">
	<fieldset>
		<legend>Editing Category</legend>
		<form id="cat_edit" method="POST" action="lib/admin-process.php?action=<?php echo($action='cat_edit');?>" onsubmit="return false;">
			<label for="cat_edit_name">Name</label>
			<div><input id="cat_edit_name" type="text" name="name" required="true" pattern="^[\w\- ]+$" /></div>
			<input type="hidden" id="cat_edit_catid" name="catid" />
			<input type="submit" value="Submit" /> <input type="button" id="cat_edit_cancel" value="Cancel" />
            <input type="hidden" name ="cat_edit_nonce" value="<?php echo ierg4210_csrf_getNonce($action);?>"/>
		</form>
	</fieldset>
</section>

<section id="productPanel">
	<fieldset>
		<legend>New Product</legend>
		<form id="prod_insert" method="POST" action="lib/admin-process.php?action=<?php echo($action='prod_insert');?>" onsubmit="return false;" enctype="multipart/form-data">
			<label for="prod_insert_catid">Category *</label>
			<div><select id="prod_insert_catid" name="catid" required="true"></select></div>

			<label for="prod_insert_name">Name *</label>
			<div><input id="prod_insert_name" type="text" name="name" required="true" pattern="^[\w\- ]+$" /></div>

			<label for="prod_insert_price">Price *</label>
			<div><input id="prod_insert_price" type="number" name="price" required="true" pattern="^[\d\.]+$" /></div>

			<label for="prod_insert_description">Description</label>
			<div><textarea id="prod_insert_description" name="description" pattern="^[\w\-, ]+$"></textarea></div>

			<label for="prod_insert_name">Image *</label>
			<div><input id="insert_file" type="file" name="file" required="true" accept="image/jpeg,image/png,image/gif" /></div>
            <input type="hidden" name ="prod_insert_nonce" value="<?php echo ierg4210_csrf_getNonce($action);?>"/>
			<input type="submit" value="Submit" />
		</form>
	</fieldset>
	<ul id="productList"></ul>

</section>
	<!-- Generate the corresponding products here -->
<section id="productEditPanel" class="hide">
		<fieldset>
			<legend >Editing Product: <span id="prod_to_edit"></span></legend>

			<form id="prod_edit" method="POST" action="lib/admin-process.php?action=<?php echo($action='prod_edit');?>" onsubmit="return false;" enctype="multipart/form-data">
				<input type="hidden" id="prod_edit_pid" name="epid" />
				<label for="prod_edit_catid"> Category* </label>
				<div><select id="prod_edit_catid" name="ecatid"></select></div>
				<label for="prod_edit_name"> Name* </label>
				<div><input id="prod_edit_name" type="text" name="ename" pattern="^[\w\- ]+$" /></div>
				<label for="prod_edit_price"> Price* </label>
				<div><input id="prod_edit_price" type="number" name="eprice"  pattern="^[\d\.]+$" /></div>

				<label for="prod_edit_description"> Description*</label>
				<div><textarea id="prod_edit_description" name="edescription" pattern="^[\w\-, ]+$"></textarea></div>

				<label for="prod_edit_file"> Image*</label>
				<div><input id ='prod_edit_file' type="file" name="file"  accept="image/jpeg,image/png,image/gif" /></div>
                <input type="hidden" name ="prod_edit_nonce" value="<?php echo ierg4210_csrf_getNonce($action);?>"/>
				<input type="submit" value="Submit" /><input type="button" id="prod_edit_cancel" value="Cancel" />
			</form>
		</fieldset>
</section>
    <section id="orderPanel">
        <fieldset>
            <legend >Transaction Records</legend>
        <ul id="orderList"></ul>
        </fieldset>
    </section>
<div class="clear"></div>
</article>
<script type="text/javascript" src="incl/myLib.js"></script>
<script type="text/javascript" src="incl/admin.js"></script>
</body>
</html>

