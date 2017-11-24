(function(){
        if(el('logout')!=null)
    el('logout').onclick = function() {
        myLib.auth({action:'logout'}, function() {
                alert("Logout successfully ");
                location.reload();
        });
    }
})();