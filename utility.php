<?php

function db_connect(){
    $conn = mysqli_connect("localhost", "root", "", "airsid");
    if(!$conn){
        my_redirect('Errore nella connessione(' .mysqli_connect_errno().')'.mysqli_connect_error());
    }
    return $conn;
}

function my_sanitize($insecure_string){
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

function registration(){
    echo "registration";
    exit;
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