<?php
include "utility.php";
is_https();

session_start();
if(!isset($_POST["username"]) || !isset($_POST["password"])) {
    echo "Parametri vuoti";
    return;
}

if($_POST["username"]=="" || $_POST["password"]==""){
    echo "Parametri vuoti";
    return;
}

$insecure_username = $_POST['username'];
$clear_password = $_POST['password'];

$conn = db_connect();
$password = sha1($clear_password);
//todo: Devo sanitizzare o no??
//    $username = my_sanitize($insecure_username);
$username = mysqli_real_escape_string($conn, $insecure_username);
$query = "SELECT * FROM users WHERE username='".$username."' AND password='".$password."'";
if(! $reply = mysqli_query($conn, $query)) {
    echo "Errore collegamento al db";
    return;
}
if(mysqli_num_rows($reply)==0){
    mysqli_close($conn);
    my_destroy_session();
    echo "Username o password errati";
    return;
}
$row = mysqli_fetch_array($reply);

// todo: Controllo inutile lo faccio già in SQL
if($row['username']!=$username || $row['password']!=$password){
    mysqli_close($conn);
    my_destroy_session();
    echo "Username o password errati";
    return;
}
mysqli_close($conn);
$_SESSION['username'] = $username;
session_write_close();
echo "OK";
return;
//    todo: è giusto questo metodo per settare la durata della sessione??
//    session_cache_expire(2);