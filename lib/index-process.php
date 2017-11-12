<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 12/11/2017
 * Time: 19:17
 */
include_once('db.inc.php');
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

    global $db;
    $db = ierg4210_DB();
    $pid = isset($_GET['pid'])?$_GET['pid']:1;
    $q = $db->prepare("SELECT * FROM products WHERE pid =(?) ;");
    if ($q->execute(array($pid)))
        return $q->fetchAll();
}
function ierg4210_fetchprodcat(){
    if (!preg_match('/^\d*$/', $_REQUEST['pid']))
        throw new Exception("invalid-pid");
    $pid = (int) $_REQUEST['pid'];
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT * FROM products WHERE pid = (?);");
    if( $q->execute(array($pid))){
        $result = $q->fetchAll();
        foreach($result as $r){
        $price    =$r['price'];
        $prodname = $r['name'];
        $catid = $r['catid'];
        $description=$r['description'];
        }
    }
    $q = $db->prepare("SELECT name FROM categories WHERE catid =(?) ;");
    if($q->execute(array($catid))){
        $result = $q->fetchAll();
        foreach($result as $r) {
            $catname = $r['name'];
        }
    }
    return array(
        Array
        ( 'pid'   => $pid,
        'catid' => $catid,
        'prodname'=> $prodname,
        'catname' => $catname,
        'description' =>$description,
        'price'=>$price
       )
    );

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