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
        session_start();
        session_regenerate_id(true);
        return $_SESSION['auth']['em'];
    }
    if (!empty($_COOKIE['auth'])) {
        if ($t = json_decode(stripslashes($_COOKIE['auth']), true)) {
            if (time() > $t['exp']) return false;
            global $db;
            $db = ierg4210_DB();
            $q= $db->prepare('SELECT * FROM user WHERE email=?');
            $q->execute(array($t['em']));
            if($r=$q->fetch()) {
                $realk = hash_hmac('sha256', $t['exp'].$r['password'], $r['salt']);
                if ($realk == $t['k']) {
                    session_start();
                    session_regenerate_id(true);
                    $_SESSION['auth'] = $t;
                    return array(
                        'email' => $t['em'],
                        'isAdmin' => $r['isAdmin']);

                }
            }

        }
    }
    return false;
}

function ierg4210_getUser(){
    $auth=ierg4210_validateCookie();
    if(!$auth) return 'You are not logged in  <br> Please login';
    else{
        return "You are logged as:<br>". $auth['email'];
    }
}
function ierg4210_csrf_getNonce($action){
    $nonce = uniqid(mt_rand(),true);
    if (!isset($_SESSION['csrf_nonce']))
        $_SESSION['csrf_nonce'] = array();
    $_SESSION['csrf_nonce'][$action] = $nonce;
    return $nonce;
}
function ierg4210_csrf_verifyNonce($action, $nonce){
    // We assume that $REQUEST['action'] is already validated
    if (isset($nonce) && $_SESSION['csrf_nonce'][$action] == $nonce) {
        if ($_SESSION['auth']==null)
            unset($_SESSION['csrf_nonce'][$action]);
        return true;
    }
    throw new Exception('csrf-attack');
}
?>