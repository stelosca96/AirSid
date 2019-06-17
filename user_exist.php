<?php
include "utility.php";

if(!isset($_REQUEST["username"]) || $_REQUEST["username"]=="") {
    echo "Errore parametri username";
    exit;
}
$insecure_username = $_REQUEST['username'];

$conn = db_connect();
$insecure_username = my_sanitize($insecure_username);
$username = mysqli_real_escape_string($conn, $insecure_username);
$query = "SELECT * FROM users WHERE username='".$username."'";
if(! $reply = mysqli_query($conn, $query)) {
    echo "Errore collegamento al db";
    exit;
}
if(mysqli_num_rows($reply)==0){
    mysqli_close($conn);
    echo "Username non esistente";
    exit;
}
mysqli_close($conn);
echo "true";
exit;
