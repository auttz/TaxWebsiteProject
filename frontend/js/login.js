const  togglepassword = document.getElementById("togglePassword");
togglepassword.addEventListener("click",function(){
    const pass = document.getElementById("password");
    if(pass.type === "password"){
        pass.type = "text";
    }else{
        pass.type = "password";
    }
})