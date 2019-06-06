<!doctype html>
<html lang="it">
<head>
    <link rel="stylesheet" href="style.css" type="text/css">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<h1>Seleziona un posto</h1>
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
                echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
            else
                echo "<td class='sedile'><input type=\"checkbox\" name=".$y.chr($x + 65)." id=".$y.chr($x + 65)."><label for=".$y.chr($x + 65).">".$y.chr($x + 65)."</label></td>\n";
        }
        echo "</tr>\n";
    }

    ?>
</table>
</section>

</body>
</html>