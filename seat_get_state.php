<?php

function db_connect(){
    $conn = mysqli_connect("localhost", "root", "", "airsid");
    if(!$conn)
        error();
    return $conn;
}

function error(){
    echo "error";
    exit;
}

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
    $query = "UPDATE seats SET user='$uID'  WHERE id='$sID'";
    // echo $query;
    if (!$reply = mysqli_query($conn, $query))
        throw new Exception("Errore set reserved");
    return "reserved";}

session_start();
session_write_close();

if(!isset($_GET["sID"]))
    error();

if(!isset($_SESSION["username"]))
    error();

$sID = $_GET["sID"];
$conn = db_connect();
$res = "error";

//todo: controllare che il codice del sedile sia reale
$sID = mysqli_real_escape_string($conn, $sID);


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









