<?php
include "utility.php";

function do_action(){
    //var_dump($_REQUEST);
    if(isset($_REQUEST['action'])) {
        switch ($_REQUEST['action']) {
//            case "login":
//                login();
//                break;
            case "logout":
                if(logged())
                    my_destroy_session();
                break;
//            case "registration":
//                if(!logged())
//                    registration();
//                else
//                    my_redirect("Sei già loggato");
//                break;
            case "delete_reservations":
                if(logged())
                    delete_reservations();
                break;
            case "booking":
                if(logged())
                    booking();
                else
                    my_redirect("Devi essere loggato per potere effettuare una prenotazione");
                break;
        }
    }
}

function booking(){
    if(!isset($_POST["reserved"]))
        my_redirect("Non ci sono posti prenotati");

    $reserved = $_POST["reserved"];
    $uID = $_SESSION["username"];

    $conn = db_connect();
    try{
        mysqli_autocommit($conn,false);

        foreach ($reserved as $sID){
            //$sID = my_sanitize($sID);
            $sID = mysqli_real_escape_string($conn, $sID);
            $sID_label = print_seat($sID);
            if(!validate_seat($sID))
                throw new Exception("Il posto $sID_label non esiste");
            $query = "UPDATE seats SET state='busy'  WHERE id='$sID' AND user='$uID'";
            if(!mysqli_query($conn, $query))
                throw new Exception("Query fallita");
            if($a=mysqli_affected_rows($conn)==0){
                $query = "INSERT INTO seats(id, state, user) VALUES ('$sID', 'busy', '$uID')";
                if(! $reply = mysqli_query($conn, $query)) {
                    throw new Exception("Il posto $sID_label è assegnato ad un altro utente");
                }
            }


        }
        if (!mysqli_commit($conn))
            throw new Exception("Commit fallito");
        mysqli_autocommit($conn,true);
        mysqli_close($conn);
    }catch (Exception $e){
        mysqli_rollback($conn);
        mysqli_autocommit($conn,true);
        mysqli_close($conn);
        delete_reservations();
        //echo  $e->getMessage();
        my_redirect($e->getMessage());
    }

}

function delete_reservations(){
    $conn = db_connect();
    $uID = $_SESSION["username"];
    $query = "DELETE FROM seats WHERE user='$uID' AND state='reserved'";
    if(!$reply = mysqli_query($conn, $query))
        my_redirect("Errore: cancellazione prenotazioni non riuscita: ");
    mysqli_close($conn);
}

function load_all_seats(){
    $conn = db_connect();
    $res = null;
    $query = "SELECT * FROM seats";
    if(! $reply = mysqli_query($conn, $query))
        my_redirect("Errore collegamento al DB");

    while(($row = mysqli_fetch_assoc($reply))) {
        $data["state"] = $row["state"];
        $data["user"] = $row["user"];

        if (logged() && $_SESSION["username"] == $data["user"])
            $data["user"] = "my";
        else
            $data["user"] = "other";
        $res[$row["id"]] = $data;
    }
    mysqli_close($conn);
    return $res;
}

function total_busy_reserved_count($length, $width, $values){
    $res["total"] = $length*$width;
    $res["busy"] = 0;
    $res["reserved"] = 0;
    $res["my"] = 0;
    if($values==null){
        $res["free"] = $res["total"];
        return $res;
    }
    foreach ($values as $value){
        //var_dump($value);
        if($value["state"] == "busy")
            $res["busy"]++;
        if ($value["state"] == "reserved" && $value["user"] == "other")
            $res["reserved"]++;
        if ($value["state"] == "reserved" && $value["user"] == "my")
            $res["my"]++;
    }
    $res["free"] = $res["total"] - ($res["busy"]+$res["reserved"]+$res["my"]);
    $res["reserved"] += $res["my"];
    return $res;
}

function style_color($sID, $res){
    if(isset($res[$sID])){
        $data = $res[$sID];
        if($data["state"]=="busy")
            return "style='color:white;background-color:red;cursor:default'";
        if($data["user"]=="my")
            return "style='color:darkgrey;background-color:yellow'";
        return "style='color:white;background-color:orange'";
    }
    return "style='color:white;background-color:greenyellow'";
}

function is_checked($sID, $res){
    if(!logged())
        return "";
    if(!isset($res[$sID]))
        return "";
    $data = $res[$sID];
    if(isset($data) && $data["user"]=="my"  && $data["state"]!="busy")
        return "checked";
    if(isset($data) && $data["state"]=="busy")
        return "disabled";
    return "";
}