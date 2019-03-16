var events = {

        tabs: function(){
            $("#form-login #link-forgot-password, #form-forgot-password #link-login").click(function() {
                
                $("#form-login, #form-forgot-password" ).toggleClass("display-none");
                $(".error, .success").remove();
            });

            $("#link-register").click(function() {
                
                $("#form-login, #form-forgot-password").addClass("display-none");
                $("#form-register").removeClass("display-none");
                $(".panel-title").text("Register");
                $(".error, .success").remove();
            });

            $("#form-register #link-login").click(function() {
                
                $(".panel-title").text("Login");
                $("#form-register").addClass("display-none");
                $("#form-login").removeClass("display-none");
                $(".error, .success").remove();
            });
        }	
}

var app = {
        init:function(){

            events.tabs();
        }   
}