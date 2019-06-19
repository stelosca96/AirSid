<?php
include "utility.php";
is_https();
session_start();

if(!isset($_POST["username"]) || !isset($_POST["password"])) {
    echo "Parametri vuoti";
    exit;
}

if($_POST["username"]=="" || $_POST["password"]==""){
    echo "Parametri vuoti";
    exit;
}

if(logged()){
    my_destroy_session();
}

$insecure_username = $_POST['username'];
$clear_password = $_POST['password'];

$conn = db_connect();
$password = sha1($clear_password);
$insecure_username = my_sanitize($insecure_username);
$username = mysqli_real_escape_string($conn, $insecure_username);
//todo: da testare
$username = strtolower($username);

$query = "SELECT * FROM users WHERE username='".$username."' AND password='".$password."'";
if(! $reply = mysqli_query($conn, $query)) {
    mysqli_close($conn);
    echo "Errore login";
    exit;
}
if(mysqli_num_rows($reply)==0){
    mysqli_close($conn);
    my_destroy_session();
    echo "Username o password errati";
    exit;
}
$row = mysqli_fetch_array($reply);

//// todo: Controllo inutile lo faccio già in SQL
//if($row['username']!=$username || $row['password']!=$password){
//    mysqli_close($conn);
//    my_destroy_session();
//    echo "Username o password errati";
//    exit;
//}
mysqli_close($conn);
set_session_value($username);

echo "OK";
exit;
