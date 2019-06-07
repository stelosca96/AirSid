<?php

function db_connect(){
    $conn = mysqli_connect("localhost", "root", "", "airsid");
    if(!$conn){
        my_redirect('Errore nella connessione(' .mysqli_connect_errno().')'.mysqli_connect_error());
    }
    return $conn;
}

function my_sanitize($insecure_string){
    //todo: da implementare
    $secure_string = $insecure_string;
    return $secure_string;
}

function  login(){
    if(!(isset($_POST['username']) && isset($_POST['password'])))
        my_redirect("Errore parametri");
    $insecure_username = $_POST['username'];
    $clear_password = $_POST['password'];

    $conn = db_connect();
    $password = sha1($clear_password);
    //todo: Devo sanitizzare o no??
//    $username = my_sanitize($insecure_username);
    $username = mysqli_real_escape_string($conn, $insecure_username);
    $query = "SELECT * FROM users WHERE username='".$username."' AND password='".$password."'";
    if(! $reply = mysqli_query($conn, $query))
        my_redirect("Errore collegamento al DB");
    if(mysqli_num_rows($reply)==0){
        my_destroy_session();
        my_redirect("Utente o password errata");
    }
    $row = mysqli_fetch_array($reply);

    // todo: Controllo inutile lo faccio già in SQL
    if($row['username']!=$username || $row['password']!=$password){
        my_destroy_session();
        my_redirect("Utente o password errata");
    }
    mysqli_close($conn);
    $_SESSION['username'] = $username;
}

function my_redirect($mex){
    //todo: rimettere header.. Non worka bene, perchè??
//    header('HTTP/1.1 307 temporary redirect');
    header("Location: index.php?mex=".urlencode($mex));
    exit;
}

function my_destroy_session(){
    $_SESSION = array();
    if(ini_get("session.use_cookies")){
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time()-3600*24, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy();
}

function user_exist(){

}

function validate_mail($emailaddress){
    $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
    return (preg_match($pattern, $emailaddress) === 1);
}

function validate_password($password){
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])|(?=.*[a-z])(?=.*[0-9])/';
    return (preg_match($pattern, $password) === 1);
}

function registration(){
    if(!(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['password_retype'])))
        my_redirect("Errore parametri");

    $insecure_username = $_POST['username'];
    $clear_password = $_POST['password'];
    $retype_password = $_POST['password_retype'];

    $username = my_sanitize($insecure_username);

    if($retype_password!=$clear_password)
        my_redirect("Le password sono diverse");

    if(!validate_mail($username))
        my_redirect("Indirizzo mail non corretto");

    if(!validate_password($clear_password))
        my_redirect("La password non rispetta i requisiti di sicurezza");


//    $conn = db_connect();
//    $password = sha1($clear_password);
//    //todo: Devo sanitizzare o no??
////    $username = my_sanitize($insecure_username);
//    $username = mysqli_real_escape_string($conn, $insecure_username);
//    $query = "SELECT * FROM users WHERE username='".$username."' AND password='".$password."'";
//    if(! $reply = mysqli_query($conn, $query))
//        my_redirect("Errore collegamento al DB");
//    if(mysqli_num_rows($reply)==0){
//        my_destroy_session();
//        my_redirect("Utente o password errata");
//    }
//    $row = mysqli_fetch_array($reply);
//
//    // todo: Controllo inutile lo faccio già in SQL
//    if($row['username']!=$username || $row['password']!=$password){
//        my_destroy_session();
//        my_redirect("Utente o password errata");
//    }
//    mysqli_close($conn);
//    $_SESSION['username'] = $username;
}

function do_action(){
    if(isset($_REQUEST['action'])) {
        switch ($_REQUEST['action']) {
            case "login":
                login();
                break;
            case "logout":
                my_destroy_session();
                break;
            case "registration":
                registration();
                break;
        }
    }
}