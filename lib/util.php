<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 12/11/2017
 * Time: 15:44
 */
include_once('db.inc.php');
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
    return $_SESSION['csrf_nonce'][$action];
}
function ierg4210_csrf_verifyNonce($action, $nonce){
    // We assume that $REQUEST['action'] is already validated
    session_start();
    if (isset($nonce) && $_SESSION['csrf_nonce'][$action] == $nonce) {
        if ($_SESSION['auth']==null)
            unset($_SESSION['csrf_nonce'][$action]);
        return true;
    }
    throw new Exception('csrf-attack');
}
?>