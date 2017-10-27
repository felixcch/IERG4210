function addEventtoButton(){
    var buttons = document.getElementsByClassName("button");
    for (var i = 0,buttons; i < buttons.length; i++) {
        (function(i){
            buttons[i].addEventListener('click', function(){
                    if (localStorage.getItem("shoppinglist") === null) {
                        var shoppinglist ={};
                    }
                    else{
                        var shoppinglist = JSON.parse(localStorage.getItem('shoppinglist'));
                    }
                    var pid = buttons[i].id.substr(3);
                    if(!shoppinglist.hasOwnProperty(pid))
                        shoppinglist[pid] = 1;
                    else {
                        alert('You already added this item. Please check in the shopping list');
                        return;
                    }
                    alert('Added '+buttons[i].name);
                    localStorage.setItem('shoppinglist', JSON.stringify(shoppinglist));
                    UpdateShoppinglist();
                    UpdateTotal();
                }
                , false);
        })(i);
    }
}
function UpdateShoppinglist(){
    var shoppinglist = JSON.parse(localStorage.getItem('shoppinglist'));
    el('shoppingitemlist').innerHTML='';
    for (var k in shoppinglist){
        (function(k){
            myLib.get({action: 'prod_fetch',pid:parseInt(k)}, function (json) {
                // loop over the server response json
                //   the expected format (as shown in Firebug):
                var listItems = [], i = 0, prod; prod = json[i];
                listItems.push('<li>',prod.name,'  <input id="q',prod.pid,'" class="inputlist"  name ="',prod.name,'" type=number value=',shoppinglist[k],' min=0 >  @',prod.price,'</li>');
                el('shoppingitemlist').innerHTML += listItems.join('');
                var inputlist = document.getElementsByClassName("inputlist");
                for (var i = 0,inputlist; i < inputlist.length; i++) {
                    (function(i){
                        inputlist[i].addEventListener('change', function(){
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
        })(k);
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
UpdateTotal();
UpdateShoppinglist();
