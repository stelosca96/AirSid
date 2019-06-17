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
        $query = "SELECT * FROM seats  WHERE user='$uID' FOR UPDATE ";
        if(!mysqli_query($conn,$query))
            throw new Exception("Query lock fallita");

        foreach ($reserved as $sID){
            //todo: controllare che il codice del sedile sia reale
            $sID = my_sanitize($sID);
            $sID = mysqli_real_escape_string($conn, $sID);
            $query = "UPDATE seats SET state='busy'  WHERE id='$sID' AND user='$uID'";
            if(!mysqli_query($conn, $query))
                throw new Exception("Query fallita");
            if(mysqli_affected_rows($conn)==0)
                throw new Exception("Il posto $sID è assegnato ad un altro utente");

        }
        if (!mysqli_commit($conn))
            throw new Exception("Commit fallito");
        mysqli_autocommit($conn,true);
        mysqli_close($conn);

    }catch (Exception $e){
        mysqli_rollback($conn);
        mysqli_autocommit($conn,true);
        mysqli_close($conn);
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
    //todo: controllo se loggato
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
    //todo: cosa sinifica liberi??
    $res["free"] = $res["total"] - ($res["busy"]+$res["reserved"]+$res["my"]);
    return $res;
}

function style_color($sID, $res){
    if(isset($res[$sID])){
        $data = $res[$sID];
        if($data["state"]=="busy")
            return "style='color:white;background-color:red'";
        if($data["user"]=="my")
            return "style='color:darkgrey;background-color:yellow'";
        return "style='color:white;background-color:orange'";
    }
    return "style='color:white;background-color:greenyellow'";
}

function is_checked($sID, $res){
    if(!logged())
        return "";
    if(isset($res[$sID]))
        $data = $res[$sID];
        if(isset($data) && $data["user"]=="my"  && $data["state"]!="busy")
            return "checked";
    return "";
}