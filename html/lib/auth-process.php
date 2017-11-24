<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 11/11/2017
 * Time: 14:54
 */

include_once('db.inc.php');
include_once('util.php');
include_once('../external/PHPMailer/PHPMailerAutoload.php');
function ierg4210_getauthtoken(){
    return  array(ierg4210_validateCookie());
}
function ierg4210_resetpwd(){
    if(!ierg4210_csrf_verifyNonce($_REQUEST['action'],$_POST['nonce'])){
        throw new exception("CSRF-attack");
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
        throw new Exception("invalid-email");
    $email = $_POST['email'];
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT 1 FROM user WHERE email=?;");
    if($q->execute(array($email))) {
        $result = $q->fetchAll();
        if(empty($result)){
            header('Content-Type: text/html; charset=utf-8');
            echo 'Incorrect email . <br/><a href="javascript:history.back();">Back</a>';
            exit();
        };
    }
    $mail = new PHPMailer(); // create a new object
    $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = true; // authentication enabled
    $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 465; // or 587
    $mail->IsHTML(true);
    $mail->Username = "ierg4210s16@gmail.com";
    $mail->Password = "badguy74103665";
    $mail->SetFrom("no-reply@secure.s16.ierg4210.ie.cuhk.edu.hk");
    $mail->Subject = "Reset your password from Felix's Online Shop";
    session_start();
    $link =
    $mail->Body = "Dear user\n
          Please click the following link to reset your password:
          ";
    $mail->AddAddress("ierg4210s16@gmail.com");

    if(!$mail->Send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
        echo "Message has been sent";
    }

}
function ierg4210_ChangePassword(){
    if(!ierg4210_csrf_verifyNonce($_REQUEST['action'],$_POST['nonce'])){
        throw new exception("CSRF-attack");
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
        throw new Exception("invalid-email");
    if (!preg_match('/^\w*$/', $_POST['old_password']))
        throw new Exception("invalid-format-password");
    if (!preg_match('/^\w*$/', $_POST['new_password']))
        throw new Exception("invalid-format-password");
    $email=$_POST['email'];
    $old_password=$_POST['old_password'];
    $new_password=$_POST['new_password'];
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT password,salt FROM user WHERE email=?;");
    if($q->execute(array($email))) {
        $result = $q->fetchAll();
        if(empty($result)){
            header('Content-Type: text/html; charset=utf-8');
            echo 'Incorrect email or password. <br/><a href="javascript:history.back();">Back to login page.</a>';
            exit();
        };
        foreach($result as $r) {
            $salt = $r['salt'];
            $dbpassword = $r['password'];
        }
    }
    $options = [
        'salt' => $salt, //write your own code to generate a suitable salt
        'cost' => 12 // the default cost is 10
    ];
    $saltedpassword = password_hash($old_password, PASSWORD_DEFAULT, $options);
    if($saltedpassword==$dbpassword){
        $newSalt =  uniqid(mt_rand(), true);
        $options = [
            'salt' => $newSalt, //write your own code to generate a suitable salt
            'cost' => 12 // the default cost is 10
        ];
        $saltedNewpassword = password_hash($new_password, PASSWORD_DEFAULT, $options);
        $q = $db->prepare("UPDATE user SET password = (?), salt=(?)  WHERE email=?;");
        if($q->execute(array($saltedNewpassword,$newSalt,$email))){
            header("Location : ../index.php");
            exit();
        }
        else {
            throw new Exception("Failed to update database");
        }
    }
}

function ierg4210_logout(){
    session_start();
    session_destroy();
    setcookie('auth',null,time()-3600,'/','',false,true);
    return true;
}
function ierg4210_verifyIp(){
    global $db;
    $ip = ierg4210_getRealIpAddr();
    $db = ierg4210_DB();
    $attemptleft=3;
    $q = $db->prepare("SELECT Attempt,lastlogin  from login_attempt WHERE ipv4 = (?) ;");
    if($q->execute(array(ip2long($ip)))){
        $result = $q->fetchAll();
        foreach($result as $r){
            if(empty($result)){
                return array(
                    Array
                    ( 'verify'=>true
                    )
                );
            };
            $attempt = $r['Attempt'];
            $attemptleft = 3 - $attempt;
            $lastlogin = $r['lastlogin'];
            if($attemptleft<=0 && time() - strtotime($lastlogin) > 1){
                $q = $db->prepare("UPDATE login_attempt  SET Attempt = 0 WHERE ipv4 = (?) ;");
                $q->execute(array(ip2long($ip)));
                return array(
                    Array
                    ( 'verify'=>true
                    )
                );
            }
        }
    };
    if($attemptleft<=0) return array(
        Array
        ( 'verify'=>false,
          'attemptleft' =>$attemptleft,
          'lastlogin'=>$lastlogin
        )
    );
    else {
        return array(
            Array
            ( 'verify'=>true,
              'attemptleft' =>$attemptleft
            )
        );
    }
}

function ierg4210_loginfail(){
            global $db;
            $db = ierg4210_DB();
            $ip = ierg4210_getRealIpAddr();
            $q = $db->prepare("INSERT OR REPLACE INTO login_attempt VALUES ((?), (SELECT CASE  WHEN  (SELECT Attempt from login_attempt WHERE ipv4=(?)) IS NOT NULL THEN Attempt ELSE 0 END  from login_attempt WHERE ipv4 = (?)) +1,CURRENT_TIMESTAMP) ;");
            $q->execute(array(ip2long($ip),ip2long($ip),ip2long($ip )));
            $login = ierg4210_verifyIp();
           # header('Content-Type: text/html; charset=utf-8');
           # echo 'Incorrect email or password. Attempt left :' . $login[0]['attemptleft'].' <br/><a href="javascript:history.back();">Back to login page.</a>';
            header("Location: ../login.php?login=fail&attemptleft=" . $login[0]['attemptleft']);
            exit();
}
function ierg4210_login(){
    $verifyip = ierg4210_verifyIp();
    if($verifyip[0]['verify']==false){
        throw new exception("Access denied");
    }
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
            ierg4210_loginfail();
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
    ierg4210_loginfail();
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