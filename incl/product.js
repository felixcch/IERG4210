(function(){
    $_GET = {};
    document.location.search.replace(/\??(?:([^=]+)=([^&]*)&?)/g, function () {
        function decode(s) {
            return decodeURIComponent(s.split("+").join(" "));
        }
        $_GET[decode(arguments[1])] = decode(arguments[2]);
    });
    function updateUI(pid,cid) {
        myLib.get({action:'cat_fetchall'}, function(json){
            // loop over the server response json
            //   the expected format (as shown in Firebug):
            for (var options = [], listItems = [],
                     i = 0, cat; cat = json[i]; i++) {
                cat.catid = parseInt(cat.catid);
                listItems.push('<li id=" ',cat.catid,'"><a  href="index.php?catid=',cat.catid,  '"  >' ,cat.name , '</a></li>');
                if( cid===cat.catid){
                    el('nav_up').innerHTML += ' > <a href="index.php?catid='+cat.catid+'">'+cat.name+'</a>';
                }
            }
            el('nav_left').innerHTML = listItems.join('');
            myLib.get({action:'prod_fetchall'}, function(json){
                // loop over the server response json
                //   the expected format (as shown in Firebug):
                for (var options = [], listItems = [],
                         i = 0, prod; prod = json[i]; i++) {
                    prod.pid = parseInt(prod.pid);
                    if (pid === prod.pid) {
                        el('nav_up').innerHTML += '> <a href="product.php?catid=' + prod.catid + '&pid=' + prod.pid + '">' + prod.name + '</a>';
                        el('product_detail').innerHTML = '<img  src="img/' + prod.pid + '.jpg"/>\n' +
                            '     <p>\n' +
                            prod.name + '</br>\n' +
                            '        Price:' + prod.price + '</br>\n' +
                            '        Description: ' + prod.description + '</br>\n' +
                            '       <button id="but'+ prod.pid+ '" name="'+ prod.name+'" class="button">Add to cart</button>\n' +
                            '   </p>';
                    }
                }
                addEventtoButton();
            });
        })

    }
    if($_GET['pid']!==undefined && $_GET['catid'] !== undefined)
    updateUI(parseInt($_GET['pid']),parseInt($_GET['catid']));
    else
        alert('invalid pid or catid');
})();