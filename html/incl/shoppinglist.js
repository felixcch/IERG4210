(function () {

    var ui = window.ui = (window.ui || {});
    var cart = ui.cart = (ui.cart || {});
    var isLogin = false;
    productlist = [];
    myLib.fetch({action: 'prod_fetchall'}, function (json) {
        for (var options = [], listItems = [],
                 i = 0, prod; prod = json[i]; i++) {
            productlist[prod.pid] = prod.price;
        }
        cart.UpdateTotal();
        cart.UpdateShoppinglist();
    });
    myLib.auth({action:'getauthtoken'},function(json){
        if(json=='false'){
            isLogin=false;
    }
    else{
            isLogin=true;
        }
    });
    el('cart').onsubmit = function(form) {
        if(isLogin==false){
            alert("Please login to perform buy action");
            return false;
        }
        cart.UpdateShoppinglist();
        el('overlay').style.display='block';
        var shoppinglist = localStorage.getItem('shoppinglist');
        myLib.authcart({action:'authbuy',list:shoppinglist},function(json){
           var returnValue = json[0];
           el('cart').elements['custom'].value=returnValue.digest;
           el('cart').elements['invoice'].value=returnValue.invoice;
           el('cart').submit();
      });
        return false;
    };
    cart.addEventtoButton= function () {
        var buttons = document.getElementsByClassName("button");
        for (var i = 0; i < buttons.length; i++) {
            (function (i) {
                buttons[i].addEventListener('click', function () {
                        if (localStorage.getItem("shoppinglist") === null) {
                            var shoppinglist = {};
                        }
                        else {
                            var shoppinglist = JSON.parse(localStorage.getItem('shoppinglist'));
                        }
                        var pid = buttons[i].id.substr(3);
                        if (!shoppinglist.hasOwnProperty(pid))
                            shoppinglist[pid] = 1;
                        else {
                            shoppinglist[pid] = parseInt(shoppinglist[pid]) + 1;
                        }
                        alert('Added ' + buttons[i].name);
                        localStorage.setItem('shoppinglist', JSON.stringify(shoppinglist));
                        cart.UpdateShoppinglist();
                        cart.UpdateTotal();
                    }
                    , false);
            })(i);
        }
    };

    cart.UpdateShoppinglist=function () {
        var shoppinglist = JSON.parse(localStorage.getItem('shoppinglist'));
        el('shoppingitemlist').innerHTML = '';
        var num=0;
        for (var k in shoppinglist) {
            num+=1;
            (function (k,num) {
                myLib.fetch({action: 'prod_fetch', pid: parseInt(k)}, function (json) {
                    // loop over the server response json
                    //   the expected format (as shown in Firebug):
                    var listItems = [], prod;
                    prod = json[0];
                    listItems.push('<li>');
                    listItems.push (prod.name, '  <input id="q', prod.pid, '" class="inputlist"  name ="', prod.name, '" type=number   value=', shoppinglist[k], ' min=0 >  @', prod.price);
                    listItems.push (' <input   type="hidden"  name ="item_number_', num, '"    value=', num, ' min=0 >  ');
                    listItems.push (' <input type="hidden"  name ="item_name_',num, '"    value=', prod.name, ' min=0 >  ');
                    listItems.push (' <input  type="hidden"  name ="amount_', num, '"    value=', prod.price, ' min=0 >  ');
                    listItems.push (' <input  type="hidden"  name ="quantity_', num, '"    value=', shoppinglist[k], ' min=0 >  ');
                    listItems.push('</li>');
                    el('shoppingitemlist').innerHTML += listItems.join('');
                    var inputlist = document.getElementsByClassName("inputlist");
                    for (var i = 0; i < inputlist.length; i++) {
                        (function (i) {
                            inputlist[i].addEventListener('change', function () {
                                    if (!inputlist[i].value.match(/^[\d]+$/)) {
                                        alert("You should enter integers only in Quantity");
                                    }
                                    inputlist[i].value = parseInt(inputlist[i].value);
                                    var shoppinglist = JSON.parse(localStorage.getItem('shoppinglist'));
                                    if (inputlist[i].value == 0) {
                                        if (confirm('deleting ' + inputlist[i].name + '  in your shopping cart. Are you sure?')) {
                                            if (delete shoppinglist[inputlist[i].id.substring(1)])
                                                alert(inputlist[i].name + ' deleted successfully');
                                            localStorage.setItem('shoppinglist', JSON.stringify(shoppinglist));
                                        }
                                        cart.UpdateShoppinglist();
                                    }
                                    else {
                                        shoppinglist[inputlist[i].id.substring(1)] = inputlist[i].value;
                                        localStorage.setItem('shoppinglist', JSON.stringify(shoppinglist));
                                    }
                                    cart.UpdateTotal();
                                }
                                , false);
                        })(i);
                    }
                });
            })(k,num);
        }
    }

    cart.UpdateTotal = function() {
        var shoppinglist = JSON.parse(localStorage.getItem('shoppinglist'));
        var total = 0;
        for (var k in shoppinglist) {
            if (productlist[k]) {
                total += parseInt(shoppinglist[k]) * parseInt(productlist[k]);
            }
        }
        el('Total').value = total;
    }
})();

