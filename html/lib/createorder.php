<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 23/11/2017
 * Time: 18:53
 */include ("db.inc.php");
$db = ierg4210_DB();
$q = $db->prepare("DROP TABLE orders");
$q->execute();
$q = $db->prepare("CREATE TABLE orders ( oid INTEGER PRIMARY KEY ,tid TEXT, username TEXT,digest TEXT, salt TEXT,tdate Date)");
$q->execute();