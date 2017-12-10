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
    if(!isInt($_GET['pid'])){
        alert('invalid-pid');
        window.location ='index.php';
    }
    function updateUI(pid) {
        myLib.fetch({action:'fetchprodcat',pid:parseInt(pid)}, function(json){
            // loop over the server response json
            //   the expected format (as shown in Firebug):
            for (var options = [], listItems = [],
                     i = 0, prod; prod = json[i]; i++) {
                    prod.pid = parseInt(prod.pid);
                    el('nav_up').innerHTML += ' > <a href="index.php?catid='+prod.catid+'">'+prod.catname+'</a>';
                    el('nav_up').innerHTML += '> <a href="product.php?pid=' + prod.pid + '">' + prod.prodname + '</a>';
                    el('product_detail').innerHTML = '<img  src="img/' + prod.pid + '.jpg"/>\n' +
                        '     <p>\n' +
                        prod.prodname + '</br>\n' +
                        '        Price:' + prod.price + '</br>\n' +
                        '        Description: ' + prod.description + '</br>\n' +
                        '       <button id="but'+ prod.pid+ '" name="'+ prod.prodname+'" class="button">Add to cart</button>\n' +
                        '   </p>';
                        prod.catid = parseInt(prod.catid);
            }
            cart.addEventtoButton();
        });
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
    }
    updateUI(parseInt($_GET['pid']));
})();