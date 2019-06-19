<?php

function check_cookies_enabled(){
    if (!(isset($_COOKIE['check_cookies']) && $_COOKIE['check_cookies'] == 'true')) {
        if (isset($_GET['check']) && $_GET['check'] == true)
                header("location: no_cookie.html");
        else {
            setcookie('check_cookies', 'true', time() + 3600);
            header("location: index.php?check=true");
        }
    }
}

////todo: usare solo una db connect
//function db_connect_ajax(){
//    $conn = mysqli_connect("localhost", "root", "", "airsid");
//    if(!$conn) {
//        echo "Errore connessione al database";
//        exit;
//    }
//    return $conn;
//}
function db_connect(){
    $conn = mysqli_connect("localhost", "s267614", "tivestom", "s267614");
    if(!$conn) {
        echo "Errore connessione al database";
        exit;
    }
    return $conn;
}

function my_sanitize($string){
    $string = htmlentities($string);
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
    $pattern = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';
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
        //echo "Riga: ", $riga,"\nColonna: ", $colonna;
        if($riga<=0 || $riga>$dim["lunghezza"] || $colonna>$dim["larghezza"] || $colonna<=0)
            return false;
        return true;
    }else return false;

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
    //la larghezza deve essere un numero pari per avere il corridoio
    $dim["larghezza"] = 6;
    return $dim;
}