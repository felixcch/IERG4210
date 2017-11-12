(function(){
        el('login').onsubmit = function(e) {
            myLib.submit(e, function() {
                alert("incorrect email or password!");
            });
        }
})();