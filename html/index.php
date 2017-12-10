<html>
<head>
<title>Felix's Online Store</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="incl/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php include_once('incl/header.php');
include_once('lib/util.php');
 ?>
<div id="nav_up" class="nav-menu">
    <a href="index.php"">Home</a>
</div>
<div id="shoppingcart" class="shoppingcart">
    <p id="shoppinglist">Shopping List Total:$
        <input id="Total" type="number" value=0 readonly/>
    </p>
    <form id="cart" method ="POST" action="https://www.sandbox.paypal.com/cgi-bin/webscr" onsubmit="return false">
        <!--<input type="hidden" name ="nonce" value="<?php echo ierg4210_csrf_getNonce('buy');?>"/>!-->
        <ul id="shoppingitemlist">
        </ul>
        <input type="hidden" name="business" value="felixchouch-facilitator@gmail.com">
        <input type="hidden" name="cmd" value="_cart" />
        <input type="hidden" name="upload" value="1" />
        <input type="hidden" name="currency_code" value="HKD" />
        <input type="hidden" name="charset" value="utf-8" />
        <input type="hidden" name="custom" value="0" />
        <input type="hidden" name="invoice" value="0" />
        <input type="hidden" name="nonce" value="<?php echo ierg4210_csrf_getNonce('authbuy')?>"/>
        <input type="submit" value="Checkout" />
    </form>
</div>
<div id="overlay" class="overlay">
    <div id="loader"></div>
</div>
<div id="content" class="content">
    <ul id = "product_list" class="productlist">

    </ul>
</div>
<nav class="nav">
    <ul id="nav_left">
    </ul>
</nav>
<?php readfile( 'incl/footer.html')?>
<script type="text/javascript" src="incl/myLib.js"></script>
<script type="text/javascript" src="incl/index.js"></script>
<script type="text/javascript" src="incl/shoppinglist.js"></script>
</body>
</html>
