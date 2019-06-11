<?php
include "airplane_functions.php";
is_https();
session_start();
do_action();
session_write_close();
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
    <script src="my.js"></script>

    <script>
        function load_seat_state(sID) {
            function set_red(sID) {
                $("#cm" + sID).css("background-color", "red").css("color", "white");
                $("#" + sID).attr("disabled", "true").attr("checked", "false");
            }
            function set_gray(sID) {
                $("#cm" + sID).css("background-color", "darkgray").css("color", "white");
                $("#" + sID).attr("disabled", "true");
            }

            function set_yellow(sID) {
                $("#cm" + sID).css("background-color", "yellow").css("color", "darkgray");
                $("#" + sID).attr("checked", "true");
            }
            function set_orange(sID) {
                $("#cm" + sID).css("background-color", "orange").css("color", "white");
                $("#" + sID).attr("checked", "false");
            }

            function set_green(sID) {
                $("#cm" + sID).css("background-color", "greenyellow").css("color", "white");
                $("#" + sID).attr("checked", "false");
            }
            <?php
                if(logged())
                    echo "uID = '".$_SESSION["username"]."'";
                else echo "uID=0";
            ?>
            //todo: decidere se farlo diventare blu quando è cliccato ed in attesa di uno stato
            //$("#cm" + sID).css("background-color", "blue");
            //todo: posso fare una get anche se cambio stato sul db??
            $.get('seat_get_state.php?sID=' + sID, function(data, status) {
                if(status!=="success" || data==="error") {
                    alert("Si è verificato un problema");
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
        echo "<button class='menuBtn' id='loginBtn'>Login</button>";
    else{
        echo "<span class='menuBtn' id='user'>".$_SESSION['username']."</span>
              <button class='menuBtn' id='logoutBtn' onclick=\"window.location.href ='index.php?action=logout'\" >Logout</button>
              <form action='index.php'>
              <button type='submit' name='action' class='menuBtn' value='refresh'>Aggiorna</button>
              <button type='submit' name='action' class='menuBtn' value='delete_reservations'>Cancella prenotazioni</button>
              </form>";
    }
    ?>
    <!-- Modal Bottone login -->
    <div id="login" class="modal">

        <div class="modal-content">
            <div class="modal-header">
                <span id="close_modal" class="close">&times;</span>
                <h2 id="modalTitle">Login</h2>
            </div>
            <div class="modal-body" id="modalLogin">
                <div id="alert_login" class="alert">
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

            <div class="modal-body" id="modalRegistrazione">
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
                    <div id="alert_pasword_not_secure" class="alert">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                        <span id="password_error">La password deve contenere un carattere alfabetico minuscolo e uno maiuscolo o un numero.</span>
                    </div>
                    <input class="loginInput" type="password" name="password_retype" placeholder="Riscrivi password"  id="password_retype" required>
                    <div id="alert_paswords_not_equals" class="alert">
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
        //todo: rifare in jquery
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
    //todo: scopo debug
    if(isset($_GET['mex']))
        echo "<div id='alert_mex' class=\"alert\"><span class=\"closebtn\" onclick=\"this.parentElement.style.display='none';\">&times;</span>". $_GET['mex']."</div>"
    ?>
    <form action="index.php" method="post">
        <?php
        $res = load_all_seats();
        //la larghezza deve essere un numero pari
        $larghezza = 6;
        $lunghezza = 10;
        $stats = total_busy_reserved_count($larghezza, $lunghezza, $res);
        ?>
        <div id="top_of_section">
        <table id="stats">
            <tr>
                <th>Posti totali</th>
                <th>Posti occupati</th>
                <th>Posti prenotati</th>
                <th>Posti liberi</th>
            </tr>
            <tr>
            <?php
                echo '<td>'.$stats["total"].'</td>
                <td>'.$stats["busy"].'</td>
                <td>'.$stats["reserved"].'</td>
                <td>'.$stats["free"].'</td>';
            ?>
            </tr>
       </table>
        <?php
        if(logged()) {
            echo "<button id='prenota_button' type='submit' name='action' value='booking'>Prenota</button>";
            //DISABILITO I CLICK SUI SEDILI SE NON SONO LOGGATO
//            echo "<script type=\"text/javascript\">$(\".container\").css(\"pointer-events\", 'default').css('cursor', 'default');</script>";
//            echo "<script type=\"text/javascript\">$(\".checkmark\").css(\"pointer-events\", 'default').css('cursor', 'default');</script>";
        }
        ?>

        </div>
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
                echo "<input type='checkbox' name='reserved[]' value='$sID' id='$sID' $checked>\n";
                $style = style_color($sID, $res);
                echo "<span onclick='load_seat_state(\"" . $sID . "\")' " . $style . " class='checkmark' id='cm" . $sID . "'>" . $sID . "</span></label></td>\n";

            }
            echo "</tr>";
        }
    ?>
    </table>
        </div>

</form>
    <?php
        if(!logged())
            echo ' <script>
                $(".container").css("pointer-events", "none");
            </script>';
    ?>


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