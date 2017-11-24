(function(){
    el('resetpwd').onsubmit = function() {
       return  myLib.submit(this, function() {
        });
    };
    myLib.auth({action:'verifyIp'},function(json){
        var ip = json[0];
        if(ip.verify==false){
            el('message').innerHTML="Access denied for 15 mins";
            el('submit').disabled=true;
        }
    });
})();