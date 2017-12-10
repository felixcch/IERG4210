<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 12/11/2017
 * Time: 15:44
 */
include_once('db.inc.php');
function ierg4210_GetTotalVisitor(){
    global $db;
    $db = ierg4210_DB();
    $q= $db->prepare('SELECT COUNT(DISTINCT ipv4) AS num FROM visitor ');

    if($q->execute()){
        return $q->fetch();
    }
}
function ierg4210_Addvisitor($ip){
    global $db;
    $db = ierg4210_DB();
    $q= $db->prepare('INSERT INTO visitor (ipv4,Date) VALUES((?),CURRENT_TIMESTAMP )');
    $q->execute(array($ip));
}
function ierg4210_validateCookie()
{
    if (!empty($_SESSION['auth']))
    {
        session_regenerate_id(true);
        return $_SESSION['auth'];
    }
    if (!empty($_COOKIE['auth'])) {
        if ($t = json_decode(stripslashes($_COOKIE['auth']), true)) {
            if (!filter_var($t['em'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid cookie");
            }
            if (time() > $t['exp']) return false;
            global $db;
            $db = ierg4210_DB();
            $q= $db->prepare('SELECT * FROM user WHERE email=?');
            $q->execute(array($t['em']));
            if($r=$q->fetch()) {
                $options = [
                    'salt' =>$r['salt'], //write your own code to generate a suitable salt
                    'cost' => 12 // the default cost is 10
                ];
                $realk = password_hash($t['exp'].$r['password'], PASSWORD_DEFAULT, $options);
                if ($realk == $t['k']) {
                    session_regenerate_id(true);
                    $_SESSION['auth'] = $t;
                    return $t;
                }
            }

        }
    }
    return false;
}
function ierg4210_getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
        $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
        $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
        $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
function ierg4210_getUserInfo(){
    $auth=ierg4210_validateCookie();
    if(!$auth) return 'Hello  <br> Visitor';
    else{
        return "Hello <br>". $auth['em'];
    }
}
function ierg4210_csrf_getNonce($action){
    session_start();
    $nonce = uniqid(mt_rand(),true);

    if (!isset($_SESSION['csrf_nonce']))
        $_SESSION['csrf_nonce'] = array();
    $_SESSION['csrf_nonce'][$action] = $nonce;
    date_default_timezone_set('Asia/Hong_Kong');
    $date = date('m/d/Y h:i:s a', time());
    error_log("[".$date."]" . " Nonce created:  ".$nonce.",action: ".$action."session [csrf_nonce][action]: ".$_SESSION['csrf_nonce'][$action]." \n", 3, "/var/www/nonce_log.txt");
    error_log(print_r($_SESSION,true), 3, "/var/www/nonce_log.txt");
    return $_SESSION['csrf_nonce'][$action];
}
function ierg4210_csrf_verifyNonce($action, $nonce){
    // We assume that $REQUEST['action'] is already validated
    session_start();
    if (isset($nonce) && $_SESSION['csrf_nonce'][$action] == $nonce) {   
        return true;
    }
    date_default_timezone_set('Asia/Hong_Kong');
    $date = date('m/d/Y h:i:s a', time());
    error_log("[".$date."]" . " Nonce verified failed:  ".$nonce.",action: ".$action.",session [csrf_nonce][action]: ".$_SESSION['csrf_nonce'][$action]." \n", 3, "/var/www/nonce_log.txt");
    error_log(print_r($_SESSION,true), 3, "/var/www/nonce_log.txt");
    throw new Exception('csrf-attack');
}
?>