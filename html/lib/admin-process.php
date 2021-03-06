<?php
include_once('db.inc.php');
include_once('util.php');
$auth = ierg4210_validateCookie();
if(!$auth) {header("Location:login.php");exit();}
else{
    if(!$auth['isAdmin']) {header("Location:index.php");
    exit();
}
}
date_default_timezone_set('Asia/Hong_Kong');
$date = date('m/d/Y h:i:s a', time());
function ierg4210_visitor_fetchall(){
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT ipv4,Date FROM visitor;");
    if ($q->execute())
        return $q->fetchAll();
}
function ierg4210_orders_fetchall(){
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT * FROM orders LIMIT 100;");
    if ($q->execute())
        return $q->fetchAll();
}
function ierg4210_cat_fetchall() {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT * FROM categories LIMIT 100;");
    if ($q->execute())
        return $q->fetchAll();
}
function ierg4210_prod_fetchall() {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT * FROM products LIMIT 100 ;");
    if ($q->execute())
        return $q->fetchAll();
}
function ierg4210_prod_fetch(){
    if (!preg_match('/^\d*$/', $_GET['pid']))
        throw new Exception("invalid-catid");
    global $db;
    $db = ierg4210_DB();
    $pid = isset($_GET['pid'])?$_GET['pid']:1;
    $q = $db->prepare("SELECT * FROM products WHERE pid =(?) ;");
    if ($q->execute(array($pid)))
        return $q->fetchAll();
}

function ierg4210_cat_insert() {
    if(!ierg4210_csrf_verifyNonce($_REQUEST['action'],$_POST['cat_insert_nonce'])){
        throw new exception("CSRF-attack");
    }
    if (!preg_match('/^[\w\- ]+$/', $_POST['name']))
        throw new Exception("invalid-name");
	// DB manipulation
	global $db;
	$db = ierg4210_DB();
	$q = $db->prepare("INSERT INTO categories (name) VALUES (?)");
    global $date;
	if($q->execute(array($_POST['name']))){
        error_log("[".$date."]" . " [Insert category] category insert successfully\n "." name:".$_POST['name']."\n", 3, "/var/www/admin_log.txt");
	    return true;
    };
    error_log("[".$date."]" . " [Insert category] category insert failed\n "." name:".$_POST['name']."\n", 3, "/var/www/admin_log.txt");
	return false;
}

function ierg4210_cat_edit() {
    if(!ierg4210_csrf_verifyNonce($_REQUEST['action'],$_POST['cat_edit_nonce'])){
        throw new exception("CSRF-attack");
    }
    if (!preg_match('/^[\w\- ]+$/', $_POST['name']))
        throw new Exception("invalid-name");
    if (!preg_match('/^\d*$/', $_POST['catid']))
        throw new Exception("invalid-catid");
    $db = ierg4210_DB();
    $q = $db->prepare("UPDATE categories SET name=(?) WHERE catid=(?)");
    global $date;
    if( $q->execute(array($_POST['name'],$_POST['catid']))){
        error_log("[".$date."]" . " [Edit category] category edit successfully\n "." New name:".$_POST['name']."\n", 3, "/var/www/admin_log.txt");
        return true;
    };
    error_log("[".$date."]" . " [Edit category] category edit failed\n "." New name:".$_POST['name']."\n", 3, "/var/www/admin_log.txt");
    return false;
}

function ierg4210_cat_delete() {

    if (!preg_match('/^\d*$/', $_POST['catid']))
        throw new Exception("invalid-catid");
	// input validation or sanitization
	$_POST['catid'] = (int) $_POST['catid'];
	// DB manipulation
	global $db;
	$db = ierg4210_DB();
	$q = $db->prepare("DELETE FROM categories WHERE catid = ?");
    global $date;
	if($q->execute(array($_POST['catid']))){
        error_log("[".$date."]" . " [Delete category] category Delete successfully\n "."Catid:".$_POST['catid']."\n", 3, "/var/www/admin_log.txt");
        return true;
    }
    error_log("[".$date."]" . " [Delete category] category Delete failed\n "."Caitd:".$_POST['catid']."\n", 3, "/var/www/admin_log.txt");
    return false;
}

// Since this form will take file upload, we use the tranditional (simpler) rather than AJAX form submission.
// Therefore, after handling the request (DB insert and file copy), this function then redirects back to admin.html
function ierg4210_prod_insert() {
	// input validation or sanitization
	// DB manipulation
	global $db;
    global $date;
	$db = ierg4210_DB();
    if(!ierg4210_csrf_verifyNonce($_REQUEST['action'],$_POST['prod_insert_nonce'])){
        throw new exception("CSRF-attack");
    }
	if (!preg_match('/^\d*$/', (int)  $_POST['catid']))
		throw new Exception("invalid-catid");
	$_POST['catid'] = (int) $_POST['catid'];
	if (!preg_match('/^[\w\- ]+$/', $_POST['name']))
		throw new Exception("invalid-name" .$_POST['name']);
	if (!preg_match('/^[\d\.]+$/', $_POST['price']))
		throw new Exception("invalid-price");
	if (!preg_match('/^[\w\- ]+$/', $_POST['description']))
		throw new Exception("invalid-text");
	$sql="INSERT INTO products (catid, name, price, description) VALUES (?, ?, ?, ?)";
	$q = $db->prepare($sql);
	// The lastInsertId() function returns the pid (primary key) resulted by the last INSERT command
	// Copy the uploaded file to a folder which can be publicly accessible at incl/img/[pid].jpg
    if ($_FILES["file"]["error"] == 0
        && ($_FILES["file"]["type"] == "image/jpeg" || $_FILES["file"]["type"] == "image/png" || $_FILES["file"]["type"] == "image/gif")
        && getimagesize($_FILES['file']['tmp_name'])
        //&& (mime_content_type($_FILES["file"]["tmp_name"])=="image/jpeg" || mime_content_type($_FILES["file"]["tmp_name"])=="image/png"||mime_content_type($_FILES["file"]["type"])=="tmp_name/gif")
        && $_FILES["file"]["size"] < 5000000) {
        if($q->execute(array($_POST['catid'],$_POST['name'],$_POST['price'],$_POST['description']))) {
            $lastId = $db->lastInsertId();
            if (move_uploaded_file($_FILES['file']['tmp_name'], getcwd() . "/../img/" . $lastId . ".jpg")) {
                // redirect back to original page; you may comment it during debug
                error_log("[".$date."]" . " [Insert product] product insert successfully\n pid".$lastId." name:".$_POST['name']."\n", 3, "/var/www/admin_log.txt");
                header('Location:../admin.php');
                exit();
            }
        }
        else{
            error_log("[".$date."]" . " [Insert product] product insert successfully\n  name:".$_POST['name']."\n", 3, "/var/www/admin_log.txt");
            echo 'Fail to insert. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
            exit();
        }
        // Note: Take care of the permission of destination folder (hints: current user is apache)
        if (move_uploaded_file($_FILES['file']['tmp_name'], getcwd() . "/../img/" . $lastId . ".jpg")) {
            // redirect back to original page; you may comment it during debug
            header('Location:../admin.php');
            exit();
        }
    }
    else{
        header('Content-Type: text/html; charset=utf-8');
        echo 'Invalid file detected. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
        exit();
    }
    header('Content-Type: text/html; charset=utf-8');
    echo 'Fail to insert. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
    exit();
}
function ierg4210_prod_edit()
{
    date_default_timezone_set('Asia/Hong_Kong');
    $date = date('m/d/Y h:i:s a', time());
    global $db;
    $db = ierg4210_DB();
    if(!ierg4210_csrf_verifyNonce($_REQUEST['action'],$_POST['prod_edit_nonce'])){
        throw new exception("CSRF-attack");
    }
    if (!preg_match('/^\d*$/', $_POST['ecatid']))
        throw new Exception("invalid-catid");
    if (!preg_match('/^\d*$/', $_POST['epid']))
        throw new Exception("invalid-pid");
    $_POST['catid'] = (int)$_POST['ecatid'];
    $_POST['pid'] = (int)$_POST['epid'];
    if (!preg_match('/^[\w\- ]+$/', $_POST['ename']))
        throw new Exception("invalid-name");
    if (!preg_match('/^[\d\.]+$/', $_POST['eprice']))
        throw new Exception("invalid-price");
    if (!preg_match('/^[\w\- ]+$/', $_POST['edescription']))
        throw new Exception("invalid-text");
    $catid = $_POST['ecatid'];
    $name = $_POST['ename'];
    $price = $_POST['eprice'];
    $description = $_POST['edescription'];
    if ($catid || !empty($name) || !empty($price) || !empty($description)) {
        $q = "UPDATE  products  SET ";
        $q .= ($catid != 0 || $catid != null) ? "catid=$catid, " : "";
        $q .= (!empty($name)) ? "name='$name', " : "";
        $q .= (!empty($price)) ? "price=$price, " : "";
        $q .= (!empty($description)) ? "description='$description' " : "";
        $q .= " WHERE pid = (?) ;";
        $q = $db->prepare($q);
    }
        // Copy the uploaded file to a folder which can be publicly accessible at incl/img/[pid].jpg
        if (!file_exists($_FILES["efile"]["tmp_name"])) {
            if($q->execute(array($_POST['epid']))){
                error_log("[".$date."]" . " [Edit product] product edit successfully withou image upload\n", 3, "/var/www/admin_log.txt");
                header('Location:../admin.php');
                exit();
            };
        }
        if ($_FILES["efile"]["error"] == 0
            && ($_FILES["efile"]["type"] == "image/jpeg" || $_FILES["efile"]["type"] == "image/png" || $_FILES["efile"]["type"] == "image/gif")
            && getimagesize($_FILES['efile']['tmp_name'])
            //&& (mime_content_type($_FILES["file"]["tmp_name"])=="image/jpeg" || mime_content_type($_FILES["file"]["tmp_name"])=="image/png"||mime_content_type($_FILES["file"]["type"])=="tmp_name/gif")
            && $_FILES["efile"]["size"] < 5000000) {
            if( $q->execute(array($_POST['epid']))){
                if (move_uploaded_file($_FILES["efile"]["tmp_name"], getcwd() . "/../img/" . $_POST['pid'] . ".jpg")) {
                    // redirect back to original page; you may comment it during debug
                    error_log("[".$date."]" . " [Edit product] Sucessful edit. File replaced successfully.Path:".getcwd() . "/../img/" . $_POST['pid'] . ".jpg".".\n", 3, "/var/www/admin_log.txt");
                    header('Location:../admin.php');
                    exit();
                }
            }
            // Note: Take care of the permission of destination folder (hints: current user is apache)
        }
    error_log("[".$date."]" . " [Edit product] product edit failed\n  name:".$_POST['name']."\n", 3, "/var/www/admin_log.txt");
    // Only an invalid file will result in the execution below
    // To replace the content-type header which was json and output an error message
    header('Content-Type: text/html; charset=utf-8');
    echo 'Invalid file detected. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
    exit();
}
function ierg4210_prod_delete(){
    global $date;
    if (!preg_match('/^\d*$/', $_POST['pid']))
        throw new Exception("invalid-pid");
    // input validation or sanitization
    $_POST['pid'] = (int) $_POST['pid'];
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("DELETE FROM  products  WHERE pid=?");
    if($q->execute(array($_POST['pid']))){
        if(unlink(getcwd() ."/../img/" . $_POST['pid'] . ".jpg"))
            error_log("[".$date."]" . " [Delete product] product deletion successful\n  pid:".$_POST['pid']."\n", 3, "/var/www/admin_log.txt");
        return true;
    }
    error_log("[".$date."]" . " [Delete product] product deletion failed\n  pid:".$_POST['pid']."\n", 3, "/var/www/admin_log.txt");
    return false;   
}

header('Content-Type: application/json');

// input validation
if (empty($_REQUEST['action']) || !preg_match('/^\w+$/', $_REQUEST['action'])) {
	echo json_encode(array('failed'=>'undefined'));
	exit();
}

// The following calls the appropriate function based to the request parameter $_REQUEST['action'],
//   (e.g. When $_REQUEST['action'] is 'cat_insert', the function ierg4210_cat_insert() is called)
// the return values of the functions are then encoded in JSON format and used as output
try {
	if (($returnVal = call_user_func('ierg4210_' . $_REQUEST['action'])) === false) {
		if ($db && $db->errorCode()) 
			error_log(print_r($db->errorInfo(), true));
		echo json_encode(array('failed'=>'1'));
	}
	echo 'while(1);' . json_encode(array('success' => $returnVal));
} catch(PDOException $e) {
	error_log($e->getMessage(),0);
	echo json_encode(array('failed'=>'error-db'));
} catch(Exception $e) {
	echo 'while(1);' . json_encode(array('failed' => $e->getMessage()));
}
?>