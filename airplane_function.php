<?php
include "utility.php";

function do_action(){
    var_dump($_REQUEST);
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
            case "delete_reservations":
                delete_reservations();
                break;
            case "booking":
                booking();
                break;
        }
    }
}

function delete_reservations(){
    $conn = db_connect();
    $uID = $_SESSION["username"];
    $query = "DELETE * FROM seats WHERE user='$uID' AND state='reserved'";
    if(!$reply = mysqli_query($conn, $query))
        my_redirect("Errore: cancellazione prenotazioni non riuscita");
    mysqli_close($conn);
}

function load_all_seats(){
    $conn = db_connect();
    $query = "SELECT * FROM seats";
    if(! $reply = mysqli_query($conn, $query))
        my_redirect("Errore collegamento al DB");

    while(($row = mysqli_fetch_assoc($reply))) {
        $data["state"] = $row["state"];
        $data["user"] = $row["user"];
        $res[$row["id"]] = $data;
    }
    mysqli_close($conn);
    return $res;
}

function total_busy_reserved_count($length, $width, $values){
    $res["total"] = $length*$width;
    $res["busy"] = 0;
    $res["reserved"] = 0;
    foreach ($values as $value){
        if($value["state"] == "busy")
            $res["busy"]++;
        if($value["state"] == "reserved")
            $res["reserved"]++;
    }
    //todo: cosa sinifica liberi??
    $res["free"] = $res["total"] - ($res["busy"]+$res["reserved"] );
    return $res;
}

function style_color($sID, $res){
    if(isset($res[$sID])){
        $data = $res[$sID];
        if($data["state"]=="busy")
            return "style='color:white;background-color:red'";
            if(isset($_SESSION["username"]))
                if($data["user"]==$_SESSION["username"])
                    return "style='color:darkgrey;background-color:yellow'";
            return "style='color:white;background-color:orange'";
        }
    if(!isset($_SESSION["username"]))
        return "style='color:white;background-color:greenyellow'";
}