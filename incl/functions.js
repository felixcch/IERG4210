
Total = 0;
$("#shoppingcart p").hover(function(){
    var html_string="";
    for(var i=0;i<shoppinglist.length;i++){
        html_string+="<li>"+shoppinglist[i]+" <input type=\"text\" id=\"" +shoppinglist[i]+"\" value=\"1\" onchange=\"updatePrice()\"> $"+getprice(shoppinglist[i])+"</li>"
    }
    $("#itemlist").html(html_string);
    Total=0;
    for(var i=0;i<shoppinglist.length;i++){

        Total+= getprice(shoppinglist[i]) * document.getElementById(shoppinglist[i]).value;
    }
    $("#shoppinglist").html("Shopping List Total:$"+Total);
});
$(document).ready(function(){
       updatePrice();
});
function load(el, catid) {
    window.location.href = "product.php?catid="+catid+"&pid="+ el.id;
}
function addtocart(pid){
    var shoppinglist = localStorage.getItem('shoppinglist');
    if(!shoppinglist){
        var shoppinglist ={};
    }
    shoppinglist[pid] = 1;
    alert(shoppinglist);
    localStorage.setItem('shoppinglist', JSON.stringify(shoppinglist));
    updatePrice();

}

function updatePrice(){
    var httpRequest;
    httpRequest = new XMLHttpRequest();
    if (!httpRequest) {
        alert('Giving up :( Cannot create an XMLHTTP instance');
        return false;
    }
    httpRequest.onreadystatechange = alertContents;
    httpRequest.open("GET", "test.php?pid=1");
    httpRequest.send();
    function alertContents() {
        if (httpRequest.readyState === XMLHttpRequest.DONE) {
            if (httpRequest.status === 200) {
                alert(httpRequest.response);
            } else {
                alert('There was a problem with the request.');
            }
        }
    }
    $("#shoppinglist").html("Shopping List Total:$"+Total);
}
