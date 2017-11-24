(function(){
       $_GET = {};
        document.location.search.replace(/\??(?:([^=]+)=([^&]*)&?)/g, function () {
        function decode(s) {
            return decodeURIComponent(s.split("+").join(" "));
        }

        $_GET[decode(arguments[1])] = decode(arguments[2]);
    });
        if($_GET['login']=='fail'){
                 myLib.auth({action:'verifyIp'},function(json){
               var ip = json[0];
               if(ip.verify==true){
                              el('message').innerHTML="Invalid email or password. Attempts left : " + ip.attemptleft;
                          }
 });
                                             
         }
        el('login').onsubmit = function(e) {
            return myLib.submit(e, function() {
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