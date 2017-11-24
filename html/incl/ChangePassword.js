(function(){

    el('ChangePassword').onsubmit = function(e) {
                if(!validatePassword()){
alert("passwords do not match");return false; 
}
        return myLib.submit(e, function() {
               alert("successful");
        });
    }
        var password = document.getElementById("new_password")
  , confirm_password = document.getElementById("confirm_new_password");

function validatePassword(){
  if(password.value != confirm_password.value) {
    return false;
  } else {
    return true;
  }
}

confirm_password.onchange = validatePassword;
})();