(function(){
    $_GET = {};
    document.location.search.replace(/\??(?:([^=]+)=([^&]*)&?)/g, function () {
        function decode(s) {
            return decodeURIComponent(s.split("+").join(" "));
        }

        $_GET[decode(arguments[1])] = decode(arguments[2]);
    });
    if($_GET['catid']===undefined){
        el('product_list').innerHTML = 'Welcome to my store. Please choose category';
    }
    else{
        fetchproduct(parseInt($_GET['catid']));
    }
    function fetchproduct(cid) {
        myLib.get({action:'prod_fetchall'}, function(json){
            // loop over the server response json
            //   the expected format (as shown in Firebug):
            for (var options = [], listItems = [],
                     i = 0, prod; prod = json[i]; i++) {
                prod.catid = parseInt(prod.catid);
                if(cid===prod.catid)
                    listItems.push('<li id="',prod.name.escapeHTML(), '"><a href = "product.php?catid=',prod.catid,'&pid=',prod.pid,'"> <img src="img/',parseInt(prod.pid),'.jpg"  > <a   href="product.php?catid=',prod.catid,'&pid=',prod.pid,'">',prod.name.escapeHTML(),'</a>$',prod.price,' </a> <button onclick="addtocart(',prod.name,')">Add to cart</button> </li>');
            }
            el('product_list').innerHTML = listItems.join('');
        });
    }

    function load(el, catid) {
        window.location.href = "product.php?catid="+catid+"&pid="+ el.id;
    }
    myLib.get({action:'cat_fetchall'}, function(json){
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