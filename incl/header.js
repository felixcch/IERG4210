(function(){
    el('logout').onclick = function(e) {
        myLib.logout({}, function() {
                alert("Logout successfully ");
                location.reload();
        });
    }
})();