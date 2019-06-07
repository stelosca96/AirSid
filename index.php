<!doctype html>
<html lang="it">
<head>
    <?php
    include "airplane_functions.php";
    session_start();
    do_action();
    session_write_close();
    ?>
    <link rel="stylesheet" href="style2.css" type="text/css">
    <script type="text/javascript" src="jquery.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sid Airline</title>
    <script type="text/javascript">
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
                if(isset($_SESSION["username"]))
                    echo "uID = '".$_SESSION["username"]."'";
                else echo "uID=0";
            ?>

            $("#cm" + sID).css("background-color", "blue");
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
    <h1>Sid Airline</h1>
</header>

<aside id="menu">
    <?php
    //todo: scopo debug
    if(isset($_GET['mex']))
        echo $_GET['mex'];
    if(!logged())
        echo "<button class='menuBtn' id='loginBtn'>Login</button>";
    else{
        echo "<span class='menuBtn' id='username'>".$_SESSION['username']."</span>
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
                <span class="close">&times;</span>
                <h2 id="modalTitle"></h2>
            </div>
            <div class="modal-body" id="modalLogin">
                <form id="formLogin" method="post" action="index.php">
                    <input class="loginInput" type="text" name="username" placeholder="Inserisci username" id="username">
                    <input class="loginInput" type="password" name="password" placeholder="Inserisci password" id="password">
                    <input type="hidden" name="action" value="login">
                    <input class="loginInput" type="submit" id="submitBtn" value="Accedi">
                </form>
                <button id="registrazioneBtn">Non sei registrato?</button>
            </div>

            <div class="modal-body" id="modalRegistrazione">
                <form id="formRegistrazione" method="post" action="index.php">
                    <input class="loginInput" type="text" name="username" placeholder="Inserisci indirizzo mail" id="username">
                    <input class="loginInput" type="password" name="password" placeholder="Inserisci password"  id="password">
                    <input class="loginInput" type="password" name="password_retype" placeholder="Riscrivi password"  id="password_retype">
                    <input type="hidden" name="action" value="registration">
                    <input class="loginInput" type="submit" id="registerBtn" value="Registrati">
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">

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

        $(`#close`).click(function() {
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
<section id="aereo">

    <?php
    $res = load_all_seats();
    //la larghezza deve essere un numero pari
    $larghezza = 6;
    $lunghezza = 10;
    $stats = total_busy_reserved_count($larghezza, $lunghezza, $res);
    echo '<table>
                <tr>
                    <th>Posti totali</th>
                    <th>Posti occupati</th>
                    <th>Posti prenotati</th>
                    <th>Posti liberi</th>
                 </tr>
                 <tr>
                    <td>'.$stats["total"].'</td>
                    <td>'.$stats["busy"].'</td>
                    <td>'.$stats["reserved"].'</td>
                    <td>'.$stats["free"].'</td>
                  </tr>
               </<table>';
    ?>
<!--     todo: da valutare se fare la fusoliera in orizzontale   -->
<!--        <table id="fusoliera2">-->
<!--            --><?php
//            //la larghezza deve essere un numero pari
//            $res = load_all_seats();
//            $larghezza = 6;
//            $lunghezza = 10;
//            var_dump(total_busy_reserved_count($larghezza, $lunghezza, $res));
//                for($x=0; $x<$larghezza+1; $x++){
//                    echo "<tr class='sedili'>\n";
//                    for($y=1; $y<=$lunghezza; $y++){
//
//                        $sID = $y.chr($x + 65);
//                    if($x == $larghezza/2)
//                        //Disegnare il corridoio
//                        echo "<tr id='corridoio' ></tr>\n";
//                    else{
//                        echo "<td><label class='container' id='cn".$sID."'>\n";
//                        echo "<input type='checkbox' id='".$sID."''>\n";
//                        $style = style_color($sID, $res);
//                        echo "<span onclick='load_seat_state(\"".$sID."\")' ".$style." class='checkmark' id='cm".$sID."'>".$sID."</span></label></td>\n";
//                    }
//                }
//                echo "</tr>\n";
//            }
//
//            //DISABILITO I CLICK SUI SEDILI SE NON SONO LOGGATO
//            if(!isset($_SESSION['username']))
//                echo "<script type=\"text/javascript\">$(\".container\").css(\"pointer-events\", \"none\");</script>";
//            ?>
<!--        </table>-->

    <form action="index.php" method="post">
    <table id="fusoliera">
        <?php
        for($y=1; $y<=$lunghezza; $y++){
            echo "<tr class='sedili'>\n";
            for($x=0; $x<$larghezza; $x++){
                $sID = $y.chr($x + 65);
                $checked = is_checked($sID, $res);
                if($x == $larghezza/2)
                    //Disegnare il corridoio
                    echo "<td id='corridoio' ></td>\n";
                echo "<td><label class='container' id='cn".$sID."'>\n";
                echo "<input type='checkbox' name='reserved[]' value='$sID' id='$sID' $checked>\n";
                $style = style_color($sID, $res);
                echo "<span onclick='load_seat_state(\"".$sID."\")' ".$style." class='checkmark' id='cm".$sID."'>".$sID."</span></label></td>\n";
            }
            echo "</tr>\n";
        }

        if(logged())
            echo "<button type='submit' name='action' value='booking'>Prenota</button>";
        else
            //DISABILITO I CLICK SUI SEDILI SE NON SONO LOGGATO
            echo "<script type=\"text/javascript\">$(\".container\").css(\"pointer-events\", \"none\");</script>";

        ?>
    </table>



    </form>

<!--    <label class='container' id='cn".$sID."'>-->
<!--    <input type='checkbox' id='1C'>-->
<!--    <span class='checkmark' id='cm1C'>1C</span></label>-->
<!---->
<!--    <script type="text/javascript">-->
<!--        $('#1C').change(load_seat_state('1C'));-->
<!--    </script>-->

</section>
</body>
</html>