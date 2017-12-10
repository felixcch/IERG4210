
(function(){
    var ui = window.ui = (window.ui || {});
    var cart = ui.cart = (ui.cart || {});
    $_GET = {};
    document.location.search.replace(/\??(?:([^=]+)=([^&]*)&?)/g, function () {
        function decode(s) {
            return decodeURIComponent(s.split("+").join(" "));
        }

        $_GET[decode(arguments[1])] = decode(arguments[2]);
    });
    function isInt(value) {
        return !isNaN(value) && (function(x) { return (x | 0) === x; })(parseFloat(value))
    }
    if($_GET['catid']===undefined){
        el('product_list').innerHTML = 'Welcome to my store. Please choose category';
    }
    else{
        if(!isInt($_GET['catid'])){
            alert('invalid-catid');
            window.location ='index.php';
        }
        fetchproduct(parseInt($_GET['catid']));
    }
    function fetchproduct(cid) {
        myLib.fetch({action:'prod_fetchall'}, function(json){
            // loop over the server response json
            //   the expected format (as shown in Firebug):
            for (var options = [], listItems = [],
                     i = 0, prod; prod = json[i]; i++) {
                prod.catid = parseInt(prod.catid);
                if(cid===prod.catid)
                    listItems.push('<li id="',prod.name.escapeHTML(), '"><a href = "product.php?pid=',prod.pid,'"> <img src="img/',parseInt(prod.pid),'.jpg"  > <a   href="product.php?pid=',prod.pid,'">',prod.name.escapeHTML(),'</a>$',prod.price,' </a> <button id="but',prod.pid,'" name="',prod.name,'" class="button">Add to cart</button> </li>');
            }
            el('product_list').innerHTML = listItems.join('');
            cart.addEventtoButton();
        });

    }

    myLib.fetch({action:'cat_fetchall'}, function(json){
        // loop over the server response json
        //   the expected format (as shown in Firebug):
        for (var options = [], listItems = [],
                 i = 0, cat; cat = json[i]; i++) {
            listItems.push('<li id=" ',cat.catid,'"><a  href="index.php?catid=',cat.catid,  '"  >' ,cat.name , '</a></li>');
            if( $_GET['catid']!==undefined && $_GET['catid']==cat.catid){
                el('nav_up').innerHTML = '<a href="index.php"">Home</a> > <a href="index.php?catid='+cat.catid+'">'+cat.name+'</a>';
            }
        }
        el('nav_left').innerHTML = listItems.join('');
    })
})();
