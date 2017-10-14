

<html>
<head>
<title>Felix's Online Store</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body>
<?php
$db = new PDO('sqlite:../cart.sqlite');
require 'html/header.html';

?>

<div id="nav-menu" class="nav-menu">
    <a href="index.php"">Home</a>
    <?php
    if($_GET['catid']){
        $q = $db->prepare("SELECT name FROM categories WHERE  catid =". $_GET['catid']);
        $q->execute();
        $result = $q->fetch(PDO::FETCH_ASSOC);
        echo '> <a href="index.php?catid='.$_GET['catid'].'">'.$result['name'].'</a>';
    }
    if($_GET['pid']){
        $q = $db->prepare("SELECT name FROM products WHERE  pid =". $_GET['pid']);
        $q->execute();
        $result = $q->fetch(PDO::FETCH_ASSOC);
        echo '> <a href="product.php?catid='.$_GET['catid'].'&pid='.$_GET['pid'].'">'.$result['name'].'</a>';
    }
    ?>
</div>
<div id="shoppingcart" class="shoppingcart">
    <p id="shoppinglist">Shopping List</p>
    <ul id="itemlist">

    </ul>
    <input type="submit" value="Checkout" />
</div>
<div id="content" class="content">
    <?php
    if($_GET['catid']){
        $q = $db->prepare("SELECT pid,name,price FROM products WHERE  catid =". $_GET['catid']);
        $q->execute();
        $result = $q->fetch(PDO::FETCH_ASSOC);
        echo '<ul id = "product_list" class="productlist">';
        while($result) {
            echo '<li id='.$result['name'] .' ><img id="'.$result['pid'] .'" src="img/'.$result['pid'].'.jpg" onclick="load(this,'.$_GET['catid'].')"/>
        <a   href="product.php?catid='.$_GET['catid'].'&'.'pid=' .$result['pid'].'">' . $result['name'] .' </a>'.
                $result['price'].'
    <button onclick="addtocart()">Add to cart</button>
    </li>';
            $result = $q->fetch(PDO::FETCH_ASSOC);
        }
        echo '</ul>';
    }
    else {
        echo ' Welcome to Home page';
    }
    ?>
</div>
<nav class="nav">
    <ul>
        <li><a id="home" href="index.php" >Home</a></li>
        <?php
        $q = $db->prepare("SELECT * FROM categories");
        $q->execute();
        $result = $q->fetch(PDO::FETCH_ASSOC);
            while($result){
                echo '<li><a id="' . $result['name'] . ' " href="index.php?catid='.$result['catid'].  '"  >' .$result['name'] . '</a></li>';
                echo "\n";
                $result = $q->fetch(PDO::FETCH_ASSOC);
            }
        ?>
    </ul>
</nav>
<?php require 'html/footer.html';?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="js/functions.js"></script>

</body>
</html>
