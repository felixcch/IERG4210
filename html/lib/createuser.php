<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 11/11/2017
 * Time: 14:54
 */

include ("db.inc.php");
$db = ierg4210_DB();
$q = $db->prepare("DROP TABLE user");
$q->execute();
$q = $db->prepare("CREATE TABLE user ( userid INT PRIMARY KEY, email TEXT, salt TEXT, password TEXT, isAdmin BOOLEAN)");
$q->execute();

$salt = uniqid(mt_rand(), true);
$options = [
    'salt' => $salt, //write your own code to generate a suitable salt
    'cost' => 12 // the default cost is 10
];
$saltedpassword = password_hash('******', PASSWORD_DEFAULT, $options);

$q = $db->prepare("INSERT INTO user VALUES (1,'ierg4210s16@gmail.com',(?),(?),1 )");
$result = $q->execute(array($salt,$saltedpassword));
$salt = uniqid(mt_rand(), true);
$options = [
    'salt' => $salt, //write your own code to generate a suitable salt
    'cost' => 12 // the default cost is 10
];
$saltedpassword = password_hash('ierg4210s16test', PASSWORD_DEFAULT, $options);
$q = $db->prepare("INSERT INTO user VALUES (2,'test@gmail.com',(?),(?),0 )");
$result = $q->execute(array($salt,$saltedpassword));
?>
