<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 11/11/2017
 * Time: 14:54
 */

include_once('db.inc.php');
include_once('util.php');
function ierg4210_logout(){
    session_destroy();
    setcookie('auth',null,time()-3600,'/','',false,true);
    return true;
}
function ierg4210_login(){
    if(!ierg4210_csrf_verifyNonce($_REQUEST['action'],$_POST['nonce'])){
        throw new exception("CSRF-attack");
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
        throw new Exception("invalid-email");
    if (!preg_match('/^\w*$/', $_POST['password']))
        throw new Exception("invalid-password");
    $email=$_POST['email'];
    $password=$_POST['password'];
    global $db;
    $db = ierg4210_DB();
    $isAdmin=false;
    $q = $db->prepare("SELECT password,salt,isAdmin FROM user WHERE email=?;");
    if($q->execute(array($email))) {
        $result = $q->fetchAll();
        if(empty($result)){
            header('Content-Type: text/html; charset=utf-8');
            echo 'Incorrect email or password. <br/><a href="javascript:history.back();">Back to login page.</a>';
            exit();
        };
        foreach($result as $r){
            $salt = $r['salt'];
            $dbpassword = $r['password'];
            $isAdmin = $r['isAdmin'];
        }
    }
    $options = [
        'salt' => $salt, //write your own code to generate a suitable salt
        'cost' => 12 // the default cost is 10
    ];
    $saltedpassword = password_hash($password, PASSWORD_DEFAULT, $options);
    if($saltedpassword==$dbpassword){
        session_start();
        session_regenerate_id(true);
        $exp = time()+3600*24*3;
        $saltedpassword = password_hash($exp.$dbpassword, PASSWORD_DEFAULT, $options);
        $token = array(
                'em' => $email,
                'exp' => $exp,
                'k' => $saltedpassword,
                'isAdmin'=>true);
            setcookie('auth',json_encode($token),$exp,'/','',false,true);
            $_SESSION['auth']  =$token;
        if($isAdmin)
            header("Location:../admin.php");
        else
            header("Location:../index.php");
        exit();
    }
    header('Content-Type: text/html; charset=utf-8');
    echo 'Incorrect email or password. <br/><a href="javascript:history.back();">Back to login page.</a>';
    exit();
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