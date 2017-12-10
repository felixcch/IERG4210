(function(){
    function onSuccess(googleUser) {
        console.log('Logged in as: ' + googleUser.getBasicProfile().getName());
    }
    function onFailure(error) {
        console.log(error);
    }
    function renderButton() {
        gapi.signin2.render('my-signin2', {
            'scope': 'profile email',
            'width': 240,
            'height': 50,
            'longtitle': true,
            'theme': 'dark',
            'onsuccess': onSuccess,
            'onfailure': onFailure
        });

    }
        el('login').onsubmit = function() {
            return myLib.submit(this, function(json) {
                  var result = json[0];
                   if(result.login=='fail'){
                       el('message').innerHTML="Incorrect email or password";
                       myLib.auth({action:'verifyIp'},function(json){
                           var ip = json[0];
                           if(ip.verify==false){
                               el('message').innerHTML="Too many failed attempts. Please try again later.";
                               el('submit').disabled=true;
                           }
                       });
                   }
                   else if(result.login=='Successful'){
                       if(result.isAdmin==true)
                           window.location='admin.php';
                       else{
                           window.location='index.php';
                       }
                   }
            });
        };
    myLib.auth({action:'verifyIp'},function(json){
        var ip = json[0];
        if(ip.verify==false){
            el('message').innerHTML="Too many failed attempts. Please try again later.";
            el('submit').disabled=true;
        }
    });
})();