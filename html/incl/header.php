<!DOCTYPE html>
<?php include_once('lib/util.php');
$auth = ierg4210_validateCookie();
session_start();
if (!isset($_SESSION['visit'])) {
ierg4210_Addvisitor(ip2long(ierg4210_getRealIpAddr()));
$_SESSION['visit'] = true;
}
?>
<html lang="en">
<body>
    <meta charset="UTF-8">
        <div class ="topmenu">
            <ul>
                <li id="welcome">Welcome to the Store</li>
                <li><a <?php  if(!$auth) {
                    echo ' href="login.php"> login';
                    } else {
                    echo ' id= "logout" href = "#" onclick="return false;">logout' . '<li id="pwdchange"><a href="ChangePassword.php">Change Password</a></li>';
                      } ?></a></li>
                <?php if($auth['isAdmin']) echo'<li><a href="admin.php">Admin</a></li>'?>
                <li ><a class="active" href="incl/game.html">BackDoor</a></li>
                <li id="loginInfo"><?php echo ierg4210_getUserInfo();?></li>
                <li id="visitorNum" class="hidden">Total visitor: <?php echo ierg4210_GetTotalVisitor()['num'];?></li>
            </ul>
        </div>
</body>
<script type="text/javascript" src="incl/myLib.js"></script>
<script type="text/javascript" src="incl/header.js"></script>
</html>