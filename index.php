<?php
//la larghezza deve essere un numero pari
include "airplane_functions.php";
$dim = getDim();
$larghezza = $dim["larghezza"];
$lunghezza = $dim["lunghezza"];
is_https();
session_start();
if(logged())
    inactivity_redirect();
do_action();
session_write_close();
$res = load_all_seats();
//todo: fare conto aggiornato dopo ogni richiesta ajax
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
    <link rel="stylesheet" href="style2.css" type="text/css">
    <script src="jquery.js"></script>
<!-- todo:   Lascio in quel file o tiro fuori??-->
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
            $("#stats_busy").text(busy)
            $("#stats_reserved").text(reserved)
            $("#stats_my").text(my)
            $("#stats_free").text(free)
            // console.log(busy)
            // console.log(total)

        }

        function load_seat_state(sID) {
            function set_red(sID) {
                $("#cm" + sID).css("background-color", "red").css("color", "white");
                $("#" + sID).attr("disabled", "true").prop('checked', false);
                let seat = {};
                seat["state"] = "busy";
                seat["user"] = "other";
                seats[sID] = seat;
                let prop = $("#" + sID).prop('checked');
                console.log(sID + " " + prop);
                count_seats();

            }
            function set_gray(sID) {
                $("#cm" + sID).css("background-color", "darkgray").css("color", "white");
                $("#" + sID).attr("disabled", "true");
            }

            function set_yellow(sID) {
                $("#cm" + sID).css("background-color", "yellow").css("color", "darkgray");
                $("#" + sID).prop('checked', true);
                let seat = {};
                seat["state"] = "reserved";
                seat["user"] = "my";
                seats[sID] = seat;
                let prop = $("#" + sID).prop('checked');
                console.log(sID + " " + prop);
                count_seats();
            }
            // function set_orange(sID) {
            //     $("#cm" + sID).css("background-color", "orange").css("color", "white");
            //     $("#" + sID).removeAttr("checked");
            // }

            function set_green(sID) {
                $("#cm" + sID).css("background-color", "greenyellow").css("color", "white");
                $("#" + sID).prop('checked', false);
                delete seats[sID];
                count_seats();
                let prop = $("#" + sID).prop('checked');
                console.log(sID + " " + prop);
            }
<!--            -->

            let data = {};
            data["sID"] = sID;
            //todo: decidere se farlo diventare blu quando è cliccato ed in attesa di uno stato
            //$("#cm" + sID).css("background-color", "blue");
            $.post('seat_get_state.php', data, function(data) {
                //alert(data);
                if((data!=="my" && data !=="reserved" && data!=="busy" && data!=="free")) {
                    alert(data);
                    console.log(data);
                    set_gray(sID)
                }
                switch (data) {
                    case "busy":
                        set_red(sID);
                        alert("Il posto selezionato è occupato");
                        break;
                    case "reserved":
                        set_yellow(sID);
                        alert("Un altro utente aveva prenotato il posto selezionato");
                        break;
                    case "free":
                        set_green(sID);
                        break;
                    case "my":
                        set_yellow(sID);
                        break;
                }
            })
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
        echo "<button class='menuBtn' id='loginBtn'>Abilita javascript per effettuare il login.</button>";
    else{
        echo "<span class='menuBtn' id='user'>".$_SESSION['username']."</span>
              <button class='menuBtn' id='logoutBtn' onclick=\"window.location.href ='index.php?action=logout'\" >Logout</button>
              <form action='index.php'>
              <button type='submit' name='action' class='menuBtn' value='refresh'>Aggiorna</button>
              <button type='submit' name='action' class='menuBtn' value='delete_reservations'>Cancella prenotazioni</button>
              </form>";
    }
    ?>
    <script>
        $("#loginBtn").text("Login");
    </script>
    <noscript>
        Il sito non funziona senza javascript abilitato
    </noscript>
    <!-- Modal Bottone login -->
    <div id="login" class="modal">

        <div class="modal-content">
            <div class="modal-header">
                <span id="close_modal" class="close">&times;</span>
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
                    <input class="loginInput" type="email" name="username" placeholder="Inserisci username" id="username" required>
                    <input class="loginInput" type="password" name="password" placeholder="Inserisci password" id="password" required>
                    <input type="hidden" name="action" value="login">
                    <input class="loginInput" type="submit" id="submitBtn" value="Accedi">
                </form>
                <button id="registrazioneBtn">Non sei registrato?</button>
            </div>
<!--            Div che contiene il form di registrazione-->
            <div class="modal-body" id="modalRegistrazione">
<!--                Alert per notificare gli errori della richiesta ajax-->
                <div id="alert_registration" class="alert">
                    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                    <span id="registration_error"></span>
                </div>
                <form id="formRegistrazione"  onsubmit="return validate_registration()">
                    <input class="loginInput" type="email" name="username" placeholder="Inserisci indirizzo mail" id="mail" required>
                    <div id="alert_mail_error" class="alert">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                        <span id="mail_error">Non hai inserito un indirizzo mail corretto.</span>
                    </div>
                    <input class="loginInput" type="password" name="password" placeholder="Inserisci password"  id="password_registration" required>
                    <div id="alert_password_not_secure" class="alert">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                        <span id="password_error">La password deve contenere almeno 5 caratteri di cui un carattere alfabetico minuscolo e uno maiuscolo o un numero.</span>
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

        $(`#loginBtn`).click(function() {
            $('.modal').css("display", "block");
            $('#modalLogin').css("display", "block");
            $('#modalRegistrazione').css("display", "none");
            $('#modalTitle').text("Login");
        });

        $(`#registrazioneBtn`).click(function() {
            $('.modal').css("display", "block");
            $('#modalLogin').css("display", "none");
            $('#modalRegistrazione').css("display", "block");
            $('#modalTitle').text("Registrazione");
        });

        $(`#close_modal`).click(function() {
            $('.modal').css("display", "none");
            $('#modalLogin').css("display", "none");
            $('#modalRegistrazione').css("display", "none");
            $('#modalTitle').text("Registrazione");
        });
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>

</aside>
<section>
    <h2>Prenota i tuoi posti:</h2>

    <?php
    if(isset($_GET['mex']))
        echo "<div id='alert_mex' class='alert'><span class='closebtn' onclick=\"this.parentElement.style.display='none';\">&times;</span>". $_GET['mex']."</div>";
    ?>
    <div id="top_of_section">
        <table id="stats">
            <tr>
                <th>Posti totali</th>
                <th>Posti acquistati</th>
                <th>Posti prenotati</th>
                <th>Le mie prenotazioni</th>
                <th>Posti liberi</th>
            </tr>
            <tr>
                <?php
                    echo '<td id="stats_total">'.$stats["total"].'</td>'.
                    '<td id="stats_busy">'.$stats["busy"].'</td>'.
                    '<td id="stats_reserved">'.$stats["reserved"].'</td>'.
                    '<td id="stats_my">'.$stats["my"].'</td>'.
                    '<td id="stats_free">'.$stats["free"].'</td>';
                ?>
            </tr>
        </table>
    </div>

    <form action="index.php" method="post">
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
                            echo "<td class='corridoio'></td>";
                        echo '</tr>';
                    }
                    echo "<tr class='sedili'>";
                    for($y=1; $y<=$lunghezza; $y++){
                        $sID = $y.chr($x + 65);
                        $checked = is_checked($sID, $res);
                        echo "<td><label class='container' id='cn" . $sID . "'>";
                        echo "<input type='checkbox' onclick='load_seat_state(\"$sID\")' name='reserved[]' value='$sID' id='$sID' $checked>\n";
                        $style = style_color($sID, $res);
                        echo "<span " . $style . " class='checkmark' id='cm" . $sID . "'>" . $sID . "</span></label></td>\n";

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