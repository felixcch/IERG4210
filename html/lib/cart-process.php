<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 23/11/2017
 * Time: 16:53
 */
include_once('db.inc.php');
include_once('util.php');

function ierg4210_getproductprice($pid){
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT price FROM products WHERE pid=?;");
    if ($q->execute(array($pid)))
        return $q->fetch()['price'];
}
function ierg4210_generateDigest($input){
    return hash('sha256',$input);
}

function ierg4210_authbuy(){
     $auth = ierg4210_validateCookie();
     if(!$auth)return false;
     if(!ierg4210_csrf_verifyNonce($_REQUEST['action'],$_GET['nonce'])){
        throw new exception("CSRF-attack");
    }
     $username =$auth['em'];
     $shoplist = json_decode($_GET['list']);
     $total =0;
     $pid_quantity_price='';
     foreach($shoplist as $key => $value ){
         $productprice = ierg4210_getproductprice($key);
         $total += $productprice*$value;
         $productprice = number_format($productprice, 2,'.','');
         $pid_quantity_price .= $key .':' .$value . '&price=' . $productprice . ',';
     }
     $total = number_format((float)$total, 2,'.','');
     $currency = "HKD";
     $salt = uniqid(mt_rand(), true);
     $merchant_email = 'felixchouch-facilitator@gmail.com';
     $string = "username:{$username},currency:{$currency},merchantemail:{$merchant_email},salt:{$salt},pid_quantity_price:{$pid_quantity_price}totalprice:{$total}";
     $productInfo = "currency:{$currency},pid_quantity_price:{$pid_quantity_price}totalprice:{$total}";
     $digest = ierg4210_generateDigest($string);
     date_default_timezone_set('Asia/Hong_Kong');
     $date = date('m/d/Y h:i:s a', time());
     error_log("[".$date."]" . " Cart info sent to paypal. Waiting for transaction confirmation...\n", 3, "/var/www/IPN_log.txt");
     error_log("Digest generated:" . $string . "\n", 3, "/var/www/IPN_log.txt");
     global $db;
     $db = ierg4210_DB();
     $q=$db->prepare("INSERT INTO orders (digest,salt,username,productInfo) VALUES(?,?,?,?)");
     $q->execute(array($digest,$salt,$username,$productInfo));
     $invocie =$db->lastInsertId();
     header('Content-Type: application/json');
      return array(array(
         'digest'=>$digest,
         'invoice'=>$invocie
     ));
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