<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 11/11/2017
 * Time: 14:54
 */
include ("db.inc.php");
$salt = mt_rand();
echo $salt;
$saltedpassword = hash_hmac('sha256',"badguy74103665",$salt);
echo $saltedpassword;
$db = ierg4210_DB();
$q = $db->prepare("CREATE TABLE user ( userid INT PRIMARY KEY, email TEXT, salt TEXT, password TEXT, isAdmin BOOLEAN)");
$q->execute();
$q = $db->prepare("INSERT INTO user VALUES (1,'admin@gmail.com',(?),(?),0 )");
$result = $q->execute(array($salt,$saltedpassword));
$salt = mt_rand();
echo $salt;
$saltedpassword = hash_hmac('sha256',"test123",$salt);
echo $saltedpassword;
$q = $db->prepare("INSERT INTO user VALUES (2,'user1@gmail.com',(?),(?),0 )");
$result = $q->execute(array($salt,$saltedpassword));
$salt = mt_rand();
echo $salt;
$saltedpassword = hash_hmac('sha256',"test123",$salt);
echo $saltedpassword;
$q = $db->prepare("INSERT INTO user VALUES (3,'user12@gmail.com',(?),(?),0 )");
$result = $q->execute(array($salt,$saltedpassword));
echo "Success";
?>