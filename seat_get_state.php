<?php

function db_connect(){
    $conn = mysqli_connect("localhost", "root", "", "airsid");
    if(!$conn){
        echo "error";
        exit;
    }
    return $conn;
}

function error(){
    echo "error";
    exit;
}

function set_free(){

}

function set_reserved(){

}

session_start();
session_write_close();

if(!isset($_GET["sID"]))
    error();

if(!isset($_SESSION["username"]))
    error();

$sID = $_GET["sID"];
$conn = db_connect();

$query = "SELECT * FROM seats WHERE id=".$sID;

if(! $reply = mysqli_query($conn, $query))
    error();

if(mysqli_num_rows($reply)==0)
    set_reserved();


mysqli_close($conn);

$data = mysqli_fetch_assoc($reply);

if($data["state"]=="busy")
    echo "busy";

if

else if(isset($_SESSION))
    if($data["user"]==$_SESSION["username"])
        return "style='color:darkgrey;background-color:yellow'";
return "style='color:white;background-color:orange'";


return $res;
