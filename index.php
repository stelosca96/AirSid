<?php
include "airplane_functions.php";
$dim = getDim();
$larghezza = $dim["larghezza"];
$lunghezza = $dim["lunghezza"];
is_https();
check_cookies_enabled();
session_start();
if(logged())
    inactivity_redirect();
do_action();
session_write_close();
$res = load_all_seats();
$stats = total_busy_reserved_count($larghezza, $lunghezza, $res);
//var_dump($_REQUEST);
?>
<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sid Airlines</title>
    <link rel="stylesheet" href="style.css" type="text/css">
    <script src="jquery.js"></script>
    <script src="my.js"></script>

    <script>
        seats = <?php echo json_encode($res); ?>;
        if (seats==null)
            seats = {};
        console.log(seats);



        function count_seats() {
            let total = <?php echo($larghezza*$lunghezza); ?>;
            let busy = 0;
            let reserved = 0;
            let my = 0;
            // console.log(seats)
            for(let x in seats){
                let seat = seats[x];
                if(seat["state"] === "busy")
                    busy++;
                if (seat["state"] === "reserved" && seat["user"] === "other")
                    reserved++;
                if (seat["state"] === "reserved" && seat["user"] === "my")
                    my++;
            }
            let free = total - busy - reserved - my;
            reserved += my;
            $("#stats_busy").text(busy);
            $("#stats_reserved").text(reserved);
            $("#stats_my").text(my);
            $("#stats_free").text(free);
            // console.log(busy)
            // console.log(total)

        }

        function are_there_booked_seats() {
            let my = 0;
            for(let x in seats){
                let seat = seats[x];
                if (seat["state"] === "reserved" && seat["user"] === "my")
                    my++;
            }
            if(my > 0)
                return true;
            set_notification("Non ci sono posti selezionati");
            return false;
        }


    </script>
</head>
<body>
<header>
    <h1>Sid Airlines</h1>
</header>

<aside id="menu">
    <?php
    if(!logged())
        echo "<button class='menuBtn' onclick='login_btn()' id='loginBtn'>Login</button>";
    else{
        echo "<span class='menuBtn' id='user'>".$_SESSION['username']."</span>
              <button class='menuBtn' id='logoutBtn' onclick=\"window.location.href ='index.php?action=logout'\" >Logout</button>
              <form action='index.php' method='get'>
                  <button type='submit' name='action' class='menuBtn' value='refresh'>Aggiorna</button>
              </form>
              <form action='index.php' method='post'>
                  <button type='submit' name='action' class='menuBtn' value='delete_reservations'>Cancella prenotazioni</button>
              </form>
              ";
    }
    ?>
    <script>
        $("#loginBtn").css("visibility", "visible");
    </script>
    <noscript>
        Il sito non funziona senza javascript abilitato.
    </noscript>
    <!-- Modal Bottone login -->
    <div id="login" class="modal">

        <div class="modal-content">
            <div class="modal-header">
                <span id="close_modal" onclick="close_modal()" class="close">&times;</span>
                <h2 id="modalTitle">Login</h2>
            </div>
            <div class="modal-body" id="modalLogin">
<!--                Div che contiene il form di login-->
                <div id="alert_login" class="alert">
<!--                    Bottone per chiudere la modal-->
                    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                    <span id="login_error"></span>
                </div>
                <form id="formLogin" onsubmit="return validate_login()">
                    <input class="loginInput" type="email" name="username" maxlength="50" placeholder="Inserisci username" id="username" required>
                    <input class="loginInput" type="password" name="password" placeholder="Inserisci password" id="password" required>
                    <input type="hidden" name="action" value="login">
                    <input class="loginInput" type="submit" id="submitBtn" value="Accedi">
                </form>
                <button id="registrazioneBtn" onclick='registration_btn()'>Non sei registrato?</button>
            </div>
<!--            Div che contiene il form di registrazione-->
            <div class="modal-body" id="modalRegistrazione">
<!--                Alert per notificare gli errori della richiesta ajax-->
                <div id="alert_registration" class="alert">
                    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                    <span id="registration_error"></span>
                </div>
                <form id="formRegistrazione"  onsubmit="return validate_registration()">
                    <input class="loginInput" type="email" name="username" maxlength="50" placeholder="Inserisci indirizzo mail" id="mail" required>
                    <div id="alert_mail_error" class="alert">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                        <span id="mail_error">Non hai inserito un indirizzo mail corretto.</span>
                    </div>
                    <input class="loginInput" type="password" name="password" placeholder="Inserisci password"  id="password_registration" required>
                    <div id="alert_password_not_secure" class="alert">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                        <span id="password_error">La password deve contenere almeno un carattere alfabetico minuscolo e uno maiuscolo o un numero.</span>
                    </div>
                    <input class="loginInput" type="password" name="password_retype" placeholder="Riscrivi password"  id="password_retype" required>
                    <div id="alert_passwords_not_equals" class="alert">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                        <span id="passwords_error">Le password inserite sono diverse.</span>
                    </div>
                    <input type="hidden" name="action" value="registration">
                    <input class="loginInput" type="submit" id="registerBtn" value="Registrati">
                </form>
            </div>
        </div>
    </div>

    <script>

        const modal = document.getElementsByClassName("modal")[0];
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>

</aside>
<section>

    <?php
    if(isset($_GET['mex']))
        echo "<div id='alert_mex' class='alert'><span class='closebtn' onclick=\"this.parentElement.style.display='none';\">&times;</span>". $_GET['mex']."</div>";
    ?>
    <div id='alert_notification' class='alert'>
        <span id="close_notification" class='closebtn' onclick="this.parentElement.style.display='none';">&times;</span>
        <span id="notification"></span>
    </div>
    <?php
    if(isset($_REQUEST['action']) && $_REQUEST['action']=="booking")
        echo '<script>set_notification("Acquisto effettuato correttamente");</script>';
    if(logged())
        echo "<h2>Prenota i tuoi posti:</h2>";
    else
        echo "<h2>Posti prenotati:</h2>";
    ?>

    <div id="top_of_section">
        <table id="stats">
            <tr>
                <th>Posti totali</th>
                <th>Posti acquistati</th>
                <th>Posti prenotati</th>
                <?php
                if (logged())
                    echo "<th>Le mie prenotazioni</th>";
                ?>
                <th>Posti liberi</th>
            </tr>
            <tr>
                <?php
                    echo '<td id="stats_total">'.$stats["total"].'</td>'.
                    '<td id="stats_busy">'.$stats["busy"].'</td>'.
                    '<td id="stats_reserved">'.$stats["reserved"].'</td>';
                    if (logged())
                        echo '<td id="stats_my">'.$stats["my"].'</td>';
                    echo '<td id="stats_free">'.$stats["free"].'</td>';
                ?>
            </tr>
        </table>
    </div>

    <form action="index.php" onsubmit="return are_there_booked_seats()" method="post">
        <?php
        //Nascondo il tasto di prenotazione se non sono loggato
        if(logged())
            echo "<button id='prenota_button' type='submit' name='action' value='booking'>Acquista</button>";
        ?>
        <div id="scroll_f">
            <table id="fusoliera">
                <?php
                for($x=0; $x<$larghezza; $x++){
                    if($x == $larghezza/2){
                        echo "<tr>";
                        for($y=1; $y<=$lunghezza; $y++)
                            echo "<td class='corridoio'>&nbsp;</td>";
                        echo '</tr>';
                    }
                    echo "<tr class='sedili'>";
                    for($y=1; $y<=$lunghezza; $y++){
                        $sID = $y.chr($x + 65);
                        $sID_label = chr($x + 65).$y;
                        $checked = is_checked($sID, $res);
                        echo "<td><label class='container' id='cn" . $sID . "'>";
                        echo "<input type='checkbox' onclick='load_seat_state(\"$sID\")' name='reserved[]' value='$sID' id='$sID' $checked>\n";
                        $style = style_color($sID, $res);
                        echo "<span " . $style . " class='checkmark' id='cm" . $sID . "'>" . $sID_label . "</span></label></td>\n";
                    }
                    echo "</tr>";
                }
                ?>
            </table>
        </div>

    </form>
    <?php
    //Disabilito i click sui sedili se non sono loggato
    if(!logged())
        echo ' <script>$(".container").css("pointer-events", "none");</script>';
    ?>

<!--    Mostro la legenda-->
    <table class="t_legend">
        <tr>
            <td><span class="legend" id="free_seat_legend">XY</span> </td>
            <td>Posto libero</td>
        </tr>
        <tr>
            <td><span class="legend" id="my_seat_legend">XY</span> </td>
            <td>Mio posto</td>
        </tr>
        <tr>
            <td><span class="legend" id="reserved_seat_legend">XY</span> </td>
            <td>Posto riservato</td>
        </tr>
        <tr>
            <td><span class="legend" id="busy_seat_legend">XY</span> </td>
            <td>Posto occupato</td>
        </tr>
    </table>
</section>
</body>
</html>