<?php
include "utility.php";
function set_free($conn, $sID){
    $query = "DELETE FROM seats WHERE id='$sID'";
    if (!$reply = mysqli_query($conn, $query))
        throw new Exception("Errore set free");
    return "free";
}

function set_reserved($conn, $sID, $uID){
    $query = "INSERT INTO seats(id, user, state) VALUES('$sID', '$uID', 'reserved')";
    // echo $query;
    if (!$reply = mysqli_query($conn, $query))
        throw new Exception("Errore set reserved");
    return "my";
}

function change_reservation($conn, $sID, $uID){
    $query = "UPDATE seats SET user='$uID'  WHERE id='$sID' AND state!='busy'";
    // echo $query;
    if (!$reply = mysqli_query($conn, $query))
        throw new Exception("Errore set reserved");
    return "reserved";}
is_https();
session_start();
if(inactivity()){
    echo ": aggiornare la pagina";
    exit;

}

session_write_close();
if(!isset($_POST["sID"])){
    echo "Nessun sedile selezionato";
    exit;
}

if(!isset($_SESSION["username"])){
    echo "Utente non loggato";
    exit;
}

$sID = $_POST["sID"];
$conn = db_connect_ajax();
$res = "Errore";

//$sID = my_sanitize($sID);
$sID = mysqli_real_escape_string($conn, $sID);

//controllo che il codice del sedile sia reale rispetti lo standard
if(!validate_seat($sID)){
    echo "Errore nome sedile";
    mysqli_close($conn);
    exit;
}



try {
    mysqli_autocommit($conn, false);

    $query = "SELECT  * FROM seats WHERE id='$sID' FOR UPDATE";

    if (!$reply = mysqli_query($conn, $query))
        throw new Exception("Errore query");

    if(mysqli_num_rows($reply)==0)
        $res = set_reserved($conn, $sID, $_SESSION["username"]);

    $data = mysqli_fetch_assoc($reply);

    if($data["user"]==$_SESSION["username"] && $data["state"]=="reserved")
        $res = set_free($conn, $sID);

    if($data["user"]!=$_SESSION["username"] && $data["state"]=="reserved")
        $res = change_reservation($conn, $sID, $_SESSION["username"]);

    if($data["state"]=="busy")
        $res = "busy";

    if (!mysqli_commit($conn))
        throw new Exception("Commit fallito");

    echo $res;
}catch (Exception $e) {
    mysqli_rollback($conn);
    echo "Rollback ".$e->getMessage();
    mysqli_autocommit($conn, true);

}

mysqli_close($conn);









