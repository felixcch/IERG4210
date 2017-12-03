(function(){
    el('forgotpwd').onsubmit = function() {
       return  myLib.submit(this, function(json) {
           var message = json[0]
           if(message.message == 'Successful'){
               alert("A reset email has been sent to your email. Please check");
               window.location = 'index.php' ;
           }
        });
    };
    myLib.auth({action:'verifyIp'},function(json){
        var ip = json[0];
        if(ip.verify==false){
            el('message').innerHTML="Access denied";
            el('submit').disabled=true;
        }
    });
})();