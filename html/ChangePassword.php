<?php session_start();
include('lib/util.php');
$auth = ierg4210_validateCookie();
if(!$auth) {header("Location:login.php");}
?>
    <html>
    <link href="incl/changepassword.css" rel="stylesheet" type="text/css">
    <div class="container">
        <div class="card card-container">
            <form id="ChangePassword" class="form-signin" method="POST" action="lib/auth-process.php?action=<?php $action = 'ChangePassword'; echo $action?>" onsubmit="return false" enctype="multipart/form-data" >
                <div><input class="form-control" id="Email" type="email" name="email" required="true" placeholder="Email"/></div>
                <div><input class="form-control" id="old_password" type="password" name="old_password" required="true" placeholder="Old password"pattern="^[\w]+$" /></div>
                <div><input class="form-control" id="new_password" type="password" name="new_password" required="true" placeholder="New password"pattern="^[\w]+$" /></div>
                                <div><input class="form-control" id="confirm_new_password" type="password" name="confirm_new_password" required="true" placeholder="Confirm New password"pattern="^[\w]+$" /></div>
                <input type="hidden" name ="nonce" value="<?php echo ierg4210_csrf_getNonce($action);?>"/>
                <input id='submit' class="btn btn-lg btn-primary btn-block btn-signin" type="submit" value="Confirm" />
                <input id="reset" class="btn btn-lg btn-primary btn-block btn-signin" type="reset" value="Reset"/>
                <a id="back" href="index.php" value="Back">Back</a>
            </form>
        </div>
    </div>
    <script type="text/javascript" src="incl/myLib.js"></script>
    <script type="text/javascript" src="incl/ChangePassword.js"></script>
    </html>
