<html>
<?php session_start();?>
<link href="incl/login.css" rel="stylesheet" type="text/css">
<div class="container">
    <div class="card card-container">
        <!-- <img class="profile-img-card" src="//lh3.googleusercontent.com/-6V8xOA6M7BA/AAAAAAAAAAI/AAAAAAAAAAA/rzlHcD0KYwo/photo.jpg?sz=120" alt="" /> -->
        <img id="profile-img" class="profile-img-card" src="//ssl.gstatic.com/accounts/ui/avatar_2x.png" />
        <p id="profile-name" class="profile-name-card"></p>
        <form  id ="forgotpwd" class="form-signin" method="POST" action="lib/auth-process.php?action=<?php $action = 'forgotpwd'; echo $action?>" onsubmit="return false" enctype="multipart/form-data">
            <span id="message" class="message">Type your email</span>
            <input type="email" id="inputEmail" class="form-control" name="email" placeholder="Email address" required autofocus>
            <input type="hidden" name ="nonce" value="<?php include('lib/util.php');echo ierg4210_csrf_getNonce($action);?>"/>
            <input id='submit' class="btn btn-lg btn-primary btn-block btn-signin" type="submit" value="Submit" />
        </form><!-- /form -->
        <a id="signin" href="login.php" class="forgot-password">
            Sign in
        </a>
        <a id="back" href="index.php">Back</a>
    </div><!-- /card-container -->
</div><!-- /container -->
<script type="text/javascript" src="incl/myLib.js"></script>
<script type="text/javascript" src="incl/forgotpwd.js"></script>
</html>