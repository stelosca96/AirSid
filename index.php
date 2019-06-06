<!doctype html>
<html lang="it">
<head>
    <link rel="stylesheet" href="style2.css" type="text/css">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>


</head>
<body>

<?php
    include "utility.php";
    session_start();
    do_action();
    session_write_close();

?>
<header id="title_bar">
    <h1>Seleziona un posto</h1>
    <?php
    if(!isset($_SESSION['username']))
        echo "<button id='loginBtn'>Login</button>";
    else{
        echo "<button id='logoutBtn' onclick=\"window.location.href ='index.php?action=logout'\" >Logout</button>";
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
                <form id="formRegistrazione">
                    <input class="loginInput" type="text" name="username" placeholder="Inserisci username" id="username">
                    <input class="loginInput" type="password" name="password" placeholder="Inserisci password"  id="password">
                    <input class="loginInput" type="password" name="password_retype" placeholder="Riscrivi password"  id="password_retype">
                    <input type="hidden" name="action" value="registration">
                    <input class="loginInput" type="submit" id="registerBtn" value="Registrati">
                </form>
            </div>
        </div>
    </div>
    <script>
        const modal = document.getElementById("login");
        const modalLogin = document.getElementById("modalLogin");
        const modalRegistrazione = document.getElementById("modalRegistrazione");
        const modalTitle = document.getElementById("modalTitle");
        const btnLogin = document.getElementById("loginBtn");
        const btnRegistrazione = document.getElementById("registrazioneBtn");
        const span = document.getElementsByClassName("close")[0];
        btnLogin.onclick = function() {
            modal.style.display = "block";
            modalLogin.style.display = "block";
            modalRegistrazione.style.display = "none";
            modalTitle.textContent = "Login";
        };

        btnRegistrazione.onclick = function() {
            modal.style.display = "block";
            modalRegistrazione.style.display = "block";
            modalTitle.textContent = "Registrazione";
            modalLogin.style.display = "none";
        };

        span.onclick = function() {
            modal.style.display = "none";
            modalLogin.style.display = "none";
            modalRegistrazione.style.display = "none";

        };
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>
</header>


<aside id="menu">
    <p>Menu</p>
    <?php
    //todo: scopo debug
    if(isset($_GET['mex']))
        echo $_GET['mex'];
    if(isset($_SESSION['username']))
        echo "<p>User: ".$_SESSION['username']."</p>";
    ?>
</aside>
<section id="aereo">

    <table id="fusoliera">
            <?php
        //la larghezza deve essere un numero pari
        $larghezza = 6;
        $lunghezza = 10;
        for($y=1; $y<=$lunghezza; $y++){
            echo "<tr class='sedili'>\n";
            for($x=0; $x<$larghezza+1; $x++){
                if($x == $larghezza/2)
                    //Disegnare il corridoio
                    echo "<td id='corridoio'></td>\n";
                else{
                    echo "<td><label class=\"container\">";
                    echo "<input type=\"checkbox\" id=".$y.chr($x + 65).">";
                    echo "<span class=\"checkmark\">".$y.chr($x + 65)."</span></label></td>\n";
                }
            }
            echo "</tr>\n";
        }

        ?>
    </table>

</body>
</html>