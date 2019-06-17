<?php

function db_connect(){
    $conn = mysqli_connect("localhost", "root", "", "airsid");
    if(!$conn){
        //todo: da gestire diversamente o da togliere proprio
        my_redirect('Errore nella connessione(' .mysqli_connect_errno().')'.mysqli_connect_error());
    }
    return $conn;
}

function my_sanitize($string){
    $string = strip_tags($string);

    return $string;
}

function my_redirect($mex){
    header('HTTP/1.1 302 temporary redirect');
    header("Location: index.php?mex=".urlencode($mex));
    exit;
}

function my_destroy_session(){
    if(!logged())
        return;
    $_SESSION = array();
    if(ini_get("session.use_cookies")){
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time()-3600*24, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy();
}



function is_https(){
    if ( !(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')){
        $redirect = 'https://' . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $redirect);
        exit();
    }
}

function validate_mail($emailaddress){
    $pattern = '/^[a-zA-Z0-9.!#$%&\'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/igm';
    return (preg_match($pattern, $emailaddress) === 1);
}

function validate_password($password){
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])|(?=.*[a-z])(?=.*[0-9])/';
    return (preg_match($pattern, $password) === 1);
}

function logged(){
    return isset($_SESSION["username"]);
}

function validate_seat($sID){
    $dim = getDim();
    $pattern = '/^[0-9]+[A-Z]+$/';
    if (preg_match($pattern, $sID) === 1){
        $arr = preg_split('/(?<=[0-9])(?=[a-z]+)/i',$sID);
        $riga = $arr[0];
        $colonna = ord($arr[1])-64;
        echo "Riga: ", $riga,"\nColonna: ", $colonna;
        if($riga<=0 || $riga>$dim["lunghezza"] || $colonna>$dim["larghezza"] || $colonna<=0)
            return false;
        return true;
    }else return false;

}

function db_connect_ajax(){
    //todo: cambiare nome utente e password
    $conn = mysqli_connect("localhost", "root", "", "airsid");
    if(!$conn) {
        echo "Errore connessione al database";
        exit;
    }
    return $conn;
}

function inactivity(){
    $t = time();
    $diff = 0;
    $durata_sessione = 2 * 60;
    $new = false;
    if (isset($_SESSION['time'])) {
        $t0 = $_SESSION['time'];
        $diff = ($t - $t0);
    } else {
        $new = true;
    }
    if ($new || ($diff > $durata_sessione)) {
        my_destroy_session();
        echo "Sessione scaduta";
        return true;
    } else {
        $_SESSION['time'] = time();
        return false;
    }

}

function inactivity_redirect(){
    if(inactivity()){
        my_redirect("Sessione scaduta");
    }
}

function set_session_value($username){
    $_SESSION['username'] = $username;
    $_SESSION['time'] = time();
}

function getDim(){
    $dim["lunghezza"] = 10;
    $dim["larghezza"] = 6;
    return $dim;
}