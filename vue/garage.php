<?php
echo "Bienvenue ". $_SESSION["firstName"] . ". Vous êtes bien connecter en tant qu" .$_SESSION["right"] == "proprietaire" ? "e propriétaire" : "'administrateur"
?>