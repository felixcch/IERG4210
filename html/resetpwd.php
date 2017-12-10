<html>
<?php session_start(); include_once("lib/util.php");
if(!isset($_GET['nonce'])){
    header('Location:index.php');exit();
}
else{
    if (!preg_match('/^[\w\.]+$/', $_GET['nonce'])){
        header('Location:index.php');exit();
    }
}
?>


<link href="incl/login.css" rel="stylesheet" type="text/css">
<div class="container">
    <div class="card card-container">
        <!-- <img class="profile-img-card" src="//lh3.googleusercontent.com/-6V8xOA6M7BA/AAAAAAAAAAI/AAAAAAAAAAA/rzlHcD0KYwo/photo.jpg?sz=120" alt="" /> -->
        <img id="profile-img" class="profile-img-card" src="//ssl.gstatic.com/accounts/ui/avatar_2x.png" />
        <p id="profile-name" class="profile-name-card"></p>
        <form  id ="resetpwd" class="form-signin" method="POST" action="lib/auth-process.php?action=<?php $action = 'resetpwd'; echo $action?>" onsubmit="return false" enctype="multipart/form-data">
            <span id="message" class="message">Type your new password</span>
            <div><input class="form-control" id="new_password" type="password" name="new_password" required="true" placeholder="New password"pattern="^[\w]+$" /></div>
            <div><input class="form-control" id="confirm_new_password" type="password" name="confirm_new_password" required="true" placeholder="Confirm New password"pattern="^[\w]+$" /></div>
            <input type="hidden" name ="csrf_nonce" value="<?php echo ierg4210_csrf_getNonce($action);?>"/>
            <input type="hidden" name ="reset_nonce" value="<?php echo $_GET['nonce']?>"/>
            <input id='submit' class="btn btn-lg btn-primary btn-block btn-signin" type="submit" value="Submit" />
        </form><!-- /form -->
        <a id="signin" href="login.php" class="forgot-password">
            Sign in
        </a>
        <a id="back" href="index.php">Back</a>
    </div><!-- /card-container -->
</div><!-- /container -->
<script type="text/javascript" src="incl/myLib.js"></script>
<script type="text/javascript" src="incl/resetpwd.js"></script>
</html>