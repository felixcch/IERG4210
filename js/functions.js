nav = ["",""];
shoppinglist=[];
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
function load_detail(el){
    if(el.id!=""){
        var id = el.id.substring(0,el.id.length-3);
        $("#content").load("html/"+id+".html");
        nav[1] = id;
    }
    else{
        $("#content").load("html/"+el.textContent+".html");
        nav[1] = el.textContent;
    }

    $("#nav-menu").html("<a href=\"#\" onclick=\"load(this)\">Home</a>" + ">" +
        "<a  href=\"#\" onclick=\"load(this)\">" + nav[0] + "</a>"+ ">"+
        "<a  href=\"#\" onclick=\"load(this)\">" + nav[1]+ "</a>");
}
function load(el, catid) {
    window.location.href = "product.php?catid="+catid+"&pid="+ el.id;
   // $("#content").load("html/" + el.textContent + ".html");
    /*
    if(el.textContent!="Home") {
        nav[0] = el.textContent;
        $("#nav-menu").html("<a href=\"#\" onclick=\"load(this)\">Home</a>" + ">" +
            "<a  href=\"#\" onclick=\"load(this)\">" + el.textContent + "</a>");
    }
    else{
        $("#nav-menu").html("<a href=\"#\" onclick=\"load(this)\">Home</a>");
    }
    */
}
function addtocart(item){
    for( var i =0; i < shoppinglist.length ;i++){
        if(shoppinglist[i]==item){
            return;
        }
    }
    shoppinglist.push(item);
    Total=0;
    for(var i=0;i<shoppinglist.length;i++){
        Total+= getprice(shoppinglist[i]);
    }
    $("#shoppinglist").html("Shopping List Total:$"+Total);
}
function getprice(product){
    if(product=="IphoneX") return 999;
    if(product=="Iphone6") return 599;
    if(product=="Iphone8") return 699;
    if(product=="IpadPro") return 1499;
}
function updatePrice(){
    Total=0;
    for(var i=0;i<shoppinglist.length;i++){
        if(shoppinglist.length==0){
            alert(shoppinglist.length);
            Total =0;
            break;
        }
        Total += getprice(shoppinglist[i]) * document.getElementById(shoppinglist[i]).value;
    }
    $("#shoppinglist").html("Shopping List Total:$"+Total);
}
