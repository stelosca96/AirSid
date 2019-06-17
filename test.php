<?php
setcookie("user", "enabled", time() + 3600);
if(count($_COOKIE) == 0)
    echo "Coookie non abilitati";