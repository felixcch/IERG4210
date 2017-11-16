<!DOCTYPE html>
<?php include_once('lib/util.php');
$auth = ierg4210_validateCookie();
?>
<html lang="en">
<body>
    <meta charset="UTF-8">
        <div class ="topmenu">
            <ul>
                <li id="welcome">Welcome to the Store</li>
                <li id="pwdchange"><a href="PasswordChange.php">Change Password</a></li>
                <li><a <?php  if(!$auth) {
                    echo ' href="login.php"> login';
                    } else {
                    echo ' id= "logout" href = "#" onclick="return false;">logout';
                      } ?></a></li>
                <li style="float:right"><a class="active" href="incl/game.html">Hack Me</a></li>
                <li id="loginInfo"><?php echo ierg4210_getUserInfo();?></li>
            </ul>
        </div>
</body>
<script type="text/javascript" src="incl/myLib.js"></script>
<script type="text/javascript" src="incl/header.js"></script>
</html>