(function () {

    var ui = window.ui = (window.ui || {});
    var cart = ui.cart = (ui.cart || {});
    productlist = [];
    myLib.get({action: 'prod_fetchall'}, function (json) {
        for (var options = [], listItems = [],
                 i = 0, prod; prod = json[i]; i++) {
            productlist[prod.pid] = prod.price;
        }
        cart.UpdateTotal();
        cart.UpdateShoppinglist();
    });

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
        for (var k in shoppinglist) {
            (function (k) {
                myLib.get({action: 'prod_fetch', pid: parseInt(k)}, function (json) {
                    // loop over the server response json
                    //   the expected format (as shown in Firebug):
                    var listItems = [], i = 0, prod;
                    prod = json[i];
                    listItems.push('<li>', prod.name, '  <input id="q', prod.pid, '" class="inputlist"  name ="', prod.name, '" type=number   value=', shoppinglist[k], ' min=0 >  @', prod.price, '</li>');
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
            })(k);
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

