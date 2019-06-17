function validate_password(password) {
    let re = /^(?=.*[a-z])(?=.*[A-Z])|(?=.*[a-z])(?=.*[0-9])/;
    return re.test(password);

}

function validateEmail(email) {
    //todo: controllare se la re è uguale a quella del php
    let re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/igm;
    return re.test(String(email).toLowerCase());
}
function validate_registration() {
    let data = {};
    data["username"] = $("#mail").val().toLowerCase();
    data["password"] = $("#password_registration").val();
    data["password2"] = $("#password_retype").val();
    if(data["username"] ===""){
        $("#alert_login").css("display", "block");
        $("#login_error").text("Username non inserito");
        return false;
    }
    if(data["password"] ===""){
        $("#alert_login").css("display", "block");
        $("#login_error").text("Password non inserita");
        return false;
    }
    if(data["password2"] ===""){
        $("#alert_login").css("display", "block");
        $("#login_error").text("Conferma password non inserita");
        return false;
    }
    if(!validateEmail(data["username"] )){
        $("#mail").css("background-color", "#f8a9ad");
        $("#alert_mail_error").css("display", "block").text("Non hai inserito un indirizzo mail corretto.");
        return false;
    }
    if(!check_password(data["password"]))
        return false;


    if(!check_equals_passwords(data["password"], data["password2"]))
        return false;

    //todo: https??
    $.post("registration.php", data, function (data) {
        if(data!=="OK") {
            alert(data);
            $("#alert_login").css("display", "block");
            $("#login_error").text(JSON.stringify(data));
            return false;
        }
        else
            window.location = window.location.pathname
    });
    return false;
}
function validate_login() {
    let data = {};
    data["username"] = $("#username").val().toLowerCase();
    data["password"] = $("#password").val();
    if(data["username"]===""){
        $("#alert_login").css("display", "block");
        $("#login_error").text("Username non inserito");
        return false;
    }
    if(data["password"]===""){
        $("#alert_login").css("display", "block");
        $("#login_error").text("Password non inserita");
        return false;
    }
    //todo: https??
    $.post("login.php", data, function (data) {
        if(data!=="OK") {
            //alert(data);
            //console.log(data);
            $("#alert_login").css("display", "block");
            $("#login_error").text(JSON.stringify(data));
            return false;
        }
        else
            window.location = window.location.pathname
    });
    return false;

}

function check_password(password){
    if(password===""){
        $("#password_registration").css("background-color", "white");
        $("#alert_password_not_secure").css("display", "none");
        return false;
    }
    if (validate_password(password)) {
        $("#alert_password_not_secure").css("display", "none");
        $("#password_registration").css("background-color", "#c1f1a2");
        return true;
    } else {
        $("#alert_password_not_secure").css("display", "block");
        //$("#password_error").text("Le password inserite sono diverse.");
        $("#password_registration").css("background-color", "#f8a9ad");
        return false;
    }
}

function check_equals_passwords(password, password2){
    if(password!==password2){
        $("#alert_passwords_not_equals").css("display", "block");
        $("#passwords_error").text("Le password inserite sono diverse.");
        $("#password_retype").css("background-color", "#f8a9ad");
        return false;

    }else {
        $("#alert_passwords_not_equals").css("display", "none");
        $("#password_retype").css("background-color", "#c1f1a2");
        return true;
    }
}

function check_mail(username){
    if(username===""){
        $("#mail").css("background-color", "white");
        $("#alert_mail_error").css("display", "none");
        return false;
    }
    if(validateEmail(username)){
        $("#mail").css("background-color", "#c1f1a2");
        $("#alert_mail_error").css("display", "none");
        console.log(JSON.stringify(username));
        $.get("user_exist.php?username=" + username, function (data) {
            console.log(data);
            if(data==="true"){
                $("#mail").css("background-color", "#f8a9ad");
                $("#alert_mail_error").css("display", "block").text("Utente già registrato");
                return false;
            }
            else return true;
        })
    }else {
        $("#mail").css("background-color", "#f8a9ad");
        $("#alert_mail_error").css("display", "block").text("Non hai inserito un indirizzo mail corretto.");
        return false;
    }
}

$(document).ready(function(){
    $("#password_registration").keyup(function(){
        let password = $("#password_registration").val();
        let password2 = $("#password_retype").val();
        check_password(password);
        if(password2!=="")
            check_equals_passwords(password, password2)
    });

    $("#password_retype").keyup(function(){

        let password = $("#password_registration").val();
        let password2 = $("#password_retype").val();
        if(password==="" || password2===""){
            $("#password_retype").css("background-color", "white");
            $("#alert_passwords_not_equals").css("display", "none");
            return false;
        }
        check_equals_passwords(password, password2);

    });
    $("#mail").keyup(function(){
        let username = $("#mail").val();
        check_mail(username);
    });
});


