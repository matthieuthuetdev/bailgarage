<?php
$fichier = __DIR__ . '/config.php';

if (file_exists($fichier)) {
    if (unlink($fichier)) {
        if (rename(__DIR__ . "/configbis.php", __DIR__ . "/config.php")) {
            echo "déployement réussi !";
        }
    } else {
        echo "Une erreur est survenue lors de la suppression du fichier 'config.php'.";
    }
} else {
    echo "Le fichier 'config.php' n'existe pas.";
    if (rename(__DIR__ . "/configbis.php", __DIR__ . "/config.php")) {
        echo "déployement réussi !";
    }
}
