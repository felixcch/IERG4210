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
                var buttons = document.getElementsByClassName("button");
                for (var i = 0,buttons; i < buttons.length; i++) {
                    (function(i){
                        buttons[i].addEventListener('click', function(){
                                var shoppinglist = JSON.parse(localStorage.getItem('shoppinglist'));
                                var pid = buttons[i].id.substr(3);
                                if(shoppinglist===undefined){
                                    var shoppinglist ={};
                                }
                                if(shoppinglist[pid]==undefined)
                                    shoppinglist[pid] = 1;
                                alert('Added '+buttons[i].name);
                                localStorage.setItem('shoppinglist', JSON.stringify(shoppinglist));
                                UpdateShoppinglist();
                                UpdateTotal();
                            }
                            , false);
                    })(i);
                }
            });
        })

    }
    function UpdateShoppinglist(){
        var shoppinglist = JSON.parse(localStorage.getItem('shoppinglist'));
        el('shoppingitemlist').innerHTML='';
        for (var k in shoppinglist){

            myLib.get({action: 'prod_fetch',pid:parseInt(k)}, function (json) {
                // loop over the server response json
                //   the expected format (as shown in Firebug):
                for (var options = [], listItems = [],
                         i = 0, prod; prod = json[i]; i++) {
                    listItems.push('<li>',prod.name,'<input id="q',prod.pid,'" class="inputlist"  name ="',prod.name,'" type=number value=',shoppinglist[k],' min=0 >',prod.price,'</li>');
                }
                el('shoppingitemlist').innerHTML += listItems.join('');
                var inputlist = document.getElementsByClassName("inputlist");
                for (var i = 0,inputlist; i < inputlist.length; i++) {
                    (function(i){
                        inputlist[i].addEventListener('focusout', function(){
                                var shoppinglist = JSON.parse(localStorage.getItem('shoppinglist'));
                                if(inputlist[i].value==0) {
                                    if(confirm('deleting ' + inputlist[i].name +'  in your shopping cart. Are you sure?')){
                                        if(delete shoppinglist[inputlist[i].id.substring(1)])
                                            alert(inputlist[i].name+ ' deleted successfully');
                                        localStorage.setItem('shoppinglist', JSON.stringify(shoppinglist));
                                        UpdateTotal();
                                    }
                                    UpdateShoppinglist();
                                }
                                else{
                                    shoppinglist[inputlist[i].id.substring(1)] = inputlist[i].value;
                                    localStorage.setItem('shoppinglist', JSON.stringify(shoppinglist));
                                    UpdateTotal();
                                }

                            }
                            , false);
                    })(i);
                }
            });
        }

    }
    function UpdateTotal(){
        var shoppinglist = JSON.parse(localStorage.getItem('shoppinglist'));
        el('Total').value=0;
        for (var k in shoppinglist) {
            (function(k){
                myLib.get({action: 'prod_fetch', pid: parseInt(k)}, function (json) {
                    // loop over the server response json
                    //   the expected format (as shown in Firebug):
                    for (var i = 0, prod; prod = json[i]; i++) {
                        el('Total').value = parseInt(el('Total').value) + parseInt(prod.price) * parseInt(shoppinglist[k]);
                    }
                });
            })(k);
        }
    }
    UpdateShoppinglist();
    UpdateTotal();
    if($_GET['pid']!==undefined && $_GET['catid'] !== undefined)
    updateUI(parseInt($_GET['pid']),parseInt($_GET['catid']));
    else
        alert('invalid pid or catid');

})();