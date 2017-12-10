
<html>
<?php session_start();?>
<link href="incl/login.css" rel="stylesheet" type="text/css">
<meta name="google-signin-client_id" content="1022850332637-verar4pp4647f5nlr9pk6ae40f42i9or.apps.googleusercontent.com">
<div class="container">
    <div class="card card-container">
        <!-- <img class="profile-img-card" src="//lh3.googleusercontent.com/-6V8xOA6M7BA/AAAAAAAAAAI/AAAAAAAAAAA/rzlHcD0KYwo/photo.jpg?sz=120" alt="" /> -->
        <img id="profile-img" class="profile-img-card" src="//ssl.gstatic.com/accounts/ui/avatar_2x.png" />
        <p id="profile-name" class="profile-name-card"></p>
        <form  id ="login" class="form-signin" method="POST" action="lib/auth-process.php?action=<?php $action = 'login'; echo $action?>" onsubmit="return false" enctype="multipart/form-data">
            <span id="message" class="message"></span>
            <input type="email" id="inputEmail" class="form-control" name="email" placeholder="Email address" required autofocus>
            <input type="password" id="inputPassword" class="form-control"  name="password"placeholder="Password" required>
            <input type="hidden" name ="nonce" value="<?php include('lib/util.php');echo ierg4210_csrf_getNonce($action);?>"/>
            <input id='submit' class="btn btn-lg btn-primary btn-block btn-signin" type="submit" value="Sign in" />
        </form><!-- /form -->
        <a id="forgot" href="forgotpwd.php" class="forgot-password">
            Forgot the password?
        </a>
        <a id="back" href="index.php">Back</a>
    </div><!-- /card-container -->
</div><!-- /container -->
<script type="text/javascript" src="incl/myLib.js"></script>
<script type="text/javascript" src="incl/login.js"></script>
<script src="https://apis.google.com/js/platform.js" async defer></script>
</html>