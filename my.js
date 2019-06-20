function validate_password(password) {
    let re = /^(?=.*[a-z])(?=.*[A-Z])|(?=.*[a-z])(?=.*[0-9])/;
    return re.test(password);

}

function seat_label(sID) {
    let sID_label = sID.match(/[a-z]+|[^a-z]+/gi);
    console.log(sID_label[1] + sID_label[0])
    return sID_label[1] + sID_label[0]
}

function validateEmail(email) {
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
    $.post("login.php", data, function (data) {
        if(data!=="OK") {
            //alert(data);
            //console.log(data);
            $("#alert_login").css("display", "block");
            $("#login_error").text(data);
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

function login_btn(){
    $('.modal').css("display", "block");
    $('#modalLogin').css("display", "block");
    $('#modalRegistrazione').css("display", "none");
    $('#modalTitle').text("Login");
}


function registration_btn(){
    $('.modal').css("display", "block");
    $('#modalLogin').css("display", "none");
    $('#modalRegistrazione').css("display", "block");
    $('#modalTitle').text("Registrazione");
}

function close_modal(){
    $('.modal').css("display", "none");
    $('#modalLogin').css("display", "none");
    $('#modalRegistrazione').css("display", "none");
    $('#modalTitle').text("Registrazione");
}

function set_notification(mex) {
    $("#alert_notification").css("display", "block");
    $("#notification").text(mex);

}

function load_seat_state(sID) {
    function set_red(sID) {
        $("#cm" + sID).css("background-color", "red").css("color", "white");
        $("#" + sID).attr('disabled', 'true').prop('checked', false).css('cursor', 'default');
        let seat = {};
        seat["state"] = "busy";
        seat["user"] = "other";
        seats[sID] = seat;
        count_seats();

    }
    function set_gray(sID) {
        $("#cm" + sID).css("background-color", "darkgray").css("color", "white");
        $("#" + sID).attr("disabled", "true");
    }

    function set_yellow(sID) {
        $("#cm" + sID).css("background-color", "yellow").css("color", "darkgray");
        let seat = {};
        seat["state"] = "reserved";
        seat["user"] = "my";
        seats[sID] = seat;
        let prop = $("#" + sID).prop('checked', true).prop('checked');
        console.log(sID + " " + prop);
        count_seats();
    }


    function set_green(sID) {
        let sID_label = seat_label(sID);
        $("#cm" + sID).css("background-color", "greenyellow").css("color", "white");
        delete seats[sID];
        count_seats();
        let prop = $("#" + sID).prop('checked', false).prop('checked');
        console.log(sID_label + " " + prop);

    }

    let data = {};
    data["sID"] = sID;
    $("#cm" + sID).css("background-color", "blue").css("color", "white");
    $.post('seat_get_state.php', data, function(data) {

        //alert(data);
        if((data!=="my" && data !=="reserved" && data!=="busy" && data!=="free")) {
            alert(data);
            console.log(data);
            set_gray(sID)
        }
        let sID_label = seat_label(sID);
        switch (data) {
            case "busy":
                set_red(sID);
                set_notification("Il posto " + sID_label + " è occupato.");
                break;
            case "reserved":
                set_yellow(sID);
                set_notification("Un altro utente aveva prenotato il posto " + sID_label + ".");
                break;
            case "free":
                set_green(sID);
                set_notification("Hai liberato il posto " + sID_label + ".");
                break;
            case "my":
                set_yellow(sID);
                set_notification("Hai prenotato il posto " + sID_label + ".");
                break;
        }
    })
}
