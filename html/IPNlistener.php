<?php namespace Listener;
require ('/var/www/html/lib/db.inc.php');
require('external/IPNlistener/PaypalIPN.php');
use PaypalIPN;

$ipn = new PaypalIPN();

// Use the sandbox endpoint during testing.
$ipn->useSandbox();
$verified = $ipn->verifyIPN();

function ierg4210_IsOrderExist($oid){
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT 1 FROM orders WHERE oid=?;");
    if ($q->execute(array($oid)))
        if(empty($q->fetch()))return false;
    return true;
}
function ierg4210_getOrderInformation($oid){
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT username,salt,digest FROM orders WHERE oid=?;");
    if ($q->execute(array($oid)))
        return $q->fetch();
}
function ierg4210_IsTidprocessd($oid){
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT tid FROM orders WHERE oid=?;");
    if ($q->execute(array($oid)))
        if(empty($q->fetch())|| $q->fetch() == null)return false;
    return true;
}

function ierg4210_getpid($product_name){
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT pid FROM products WHERE name=?;");
    if ($q->execute(array($product_name)))
        return $q->fetch()['pid'];
    else{
        error_log("failed to get pid : \n" . $product_name, 3, "/var/www/html/log.txt");
    }
}
if ($verified) {
    /*
     * Process IPN
     * A list of variables is available here:
     * https://developer.paypal.com/webapps/developer/docs/classic/ipn/integration-guide/IPNandPDTVariables/
     */
    date_default_timezone_set('Asia/Hong_Kong');
    $date = date('m/d/Y h:i:s a', time());
    error_log("[".$date."]" . " IPN Verified \n", 3, "/var/www/log.txt");
    error_log(print_r($_POST,true),3,"/var/www/log.txt");
    $db =ierg4210_DB();
    error_log("Database connected \n", 3, "/var/www/log.txt");

    $totalitem = $_POST['num_cart_items'];
    $list ="";

    for ($i=1; $i<=$totalitem;$i++){
        $i = (string) $i;
        $item_name = $_POST['item_name' . $i ];
        $item_quantity = $_POST['quantity' . $i];
        $item_amount = number_format($_POST['mc_gross_' . $i]/$item_quantity,2,'.','');
        $item_pid = ierg4210_getpid($item_name);
        $list .="{$item_pid}:{$item_quantity}&price={$item_amount},";
    }
    error_log("list initialized:" . $list ."\n", 3, "/var/www/log.txt");
    $merchant_email = $_POST['business'];
    $total = $_POST['mc_gross'];
    $invoice  = $_POST['invoice'];
    $currency = $_POST['mc_currency'];
    $txn_id = $_POST['txn_id'];
    $txn_type =$_POST['txn_type'];
    $digest_custom = $_POST['custom'];
    error_log("Transaction id :".$txn_id."\n", 3, "/var/www/log.txt");

    if(!ierg4210_IsOrderExist($invoice)){
        error_log("The order does not exist\n", 3, "/var/www/log.txt");
        exit();
    }

    $info=ierg4210_getOrderInformation($invoice);
    $username = $info['username'];
    $salt = $info['salt'];
    $db_digest = $info['digest'];
    $string = "username:{$username},currency:{$currency},merchantemail:{$merchant_email},salt:{$salt},pid_quantity_price:{$list}totalprice:{$total}";
    $digest_from_post = hash('sha256',$string);
    error_log("String =".$string."\ntxn_type=".$txn_type.",  digest from post =". $digest_from_post.", digest_custom = " .$digest_custom.", db_digest=".$db_digest."\n", 3, "/var/www/log.txt");
    if(ierg4210_IsTidprocessd($invoice) || $txn_type!='cart' || $digest_from_post!=$digest_custom || $digest_custom!=$db_digest)
        exit();
    $q = $db->prepare("UPDATE orders  SET tid = (?),tdate=CURRENT_TIMESTAMP WHERE oid = (?) ");
    $q->execute(array($txn_id,$invoice));
    error_log("Transaction  Inserted. Order id : " .$invoice."\n", 3, "/var/www/log.txt");
}

// Reply with an empty 200 response to indicate to paypal the IPN was received correctly.
header("HTTP/1.1 200 OK");
