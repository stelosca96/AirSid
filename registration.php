<?php
//todo: https??
include "utility.php";
is_https();
session_start();

if(!(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['password2']))){
    echo "Errore parametri";
    exit;
}

$insecure_username = $_POST['username'];
$clear_password = $_POST['password'];
$retype_password = $_POST['password2'];


$conn = db_connect();
$password = sha1($clear_password);
//todo: Devo sanitizzare o no??
//    $username = my_sanitize($insecure_username);
$username = mysqli_real_escape_string($conn, $insecure_username);


if(!validate_mail($username)) {
    mysqli_close($conn);
    echo "Indirizzo mail non corretto";
    exit;
}

if(!validate_password($clear_password)) {
    mysqli_close($conn);
    echo "La password non rispetta i requisiti di sicurezza";
    exit;
}

if($retype_password!=$clear_password) {
    mysqli_close($conn);
    echo "Le password sono diverse";
    exit;
}

$query = "INSERT INTO users(username, password) VALUES ('$username', '$password')";
if(! $reply = mysqli_query($conn, $query)) {
    echo "Utente già registrato";
    mysqli_close($conn);
    exit;
}
mysqli_close($conn);
$_SESSION['username'] = $username;
echo "OK";
