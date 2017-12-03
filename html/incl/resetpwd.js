(function(){
    $_GET = {};
    document.location.search.replace(/\??(?:([^=]+)=([^&]*)&?)/g, function () {
        function decode(s) {
            return decodeURIComponent(s.split("+").join(" "));
        }
        $_GET[decode(arguments[1])] = decode(arguments[2]);
    });
    var password = document.getElementById("new_password")
        , confirm_password = document.getElementById("confirm_new_password");

    function validatePassword(){
        if(password.value != confirm_password.value) {
            return false;
        } else {
            return true;
        }
    }
    var nonce = $_GET['nonce'];
    if(!nonce.match(/^[\w\\.]+$/)){
        alert("invalid nonce");
        window.location = 'index.php' ;
    }else{
        myLib.auth({action:'verifyResetNonce',nonce:nonce},function(json){
            var nonce = json[0];
            if(nonce.verify==false){
                window.location.href="index.php";
            }
            else {
                el('resetpwd').onsubmit = function () {
                    if (!validatePassword()) {
                        alert("passwords do not match");
                        return false;
                    }
                        return myLib.submit(this, function (json) {
                            var message = json[0]
                            if(message.message == 'Successful'){
                                alert("Successful password reset");
                                window.location = 'index.php' ;
                            }
                        });
                    }
                    ;
                }
        });
    }
})();