(function(){

    el('ChangePassword').onsubmit = function() {
        return myLib.submit(this, function(json) {
               if(el('new_password').value!=el('confirm_new_password').value){
                   alert('new passwords does not match');
                   el('new_password').value='';
                   el('confirm_new_password').value='';
                   return false;
               }
               var message = json[0];
               if(message.message=='Successful'){
                    alert("Changed successfully! Please login again");
                    window.location = 'login.php'
            }
            else{
               alert('incorrect email or password!');
        }
        });
    };
})();