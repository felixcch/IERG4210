<html>
<head>
    <title>Felix's Online Store</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="incl/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
readfile('html/header.html');
?>
<div id="nav_up" class="nav-menu">
    <a href="index.php"">Home</a>
</div>
<div id="shoppingcart" class="shoppingcart">
    <p id="shoppinglist">Shopping List Total:$ <input id="Total" type="number" value=0 min=0 readonly/></p>
    <ul id="shoppingitemlist">
    </ul>
    <input type="submit" value="Checkout" />
</div>
<div id="content" class="content">
<div id='product_detail' class="product_detail">
</div>
</div>
<nav  class="nav">
    <ul id = 'nav_left'>
    </ul>
</nav>
<?php readfile('html/footer.html');?>
<script type="text/javascript" src="incl/myLib.js"></script>
<script type="text/javascript" src="incl/product.js"> </script>
</body>
</html>



