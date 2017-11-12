<?php session_start();?>
<html>
<section id="categoryPanel">
    <fieldset>
        <legend>Login</legend>
        <form id="login" method="POST" action="lib/auth-process.php?action=<?php echo($action='login');?>" onsubmit="return false" enctype="multipart/form-data" >
            <label for="Email">Email</label>
            <div><input id="Email" type="email" name="email" required="true"/></div>
            <label for="Password">Password</label>
            <div><input id="Password" type="password" name="password" required="true" pattern="^[\w]+$" /></div>
            <input type="hidden" name ="nonce" value="<?php include('lib/util.php');echo ierg4210_csrf_getNonce($action);?>"/>
            <input type="submit" value="Submit" />
        </form>
    </fieldset>
</section>
<script type="text/javascript" src="incl/myLib.js"></script>
<script type="text/javascript" src="incl/login.js"></script>
</html>
