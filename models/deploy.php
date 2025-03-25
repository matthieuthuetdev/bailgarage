<?php

unlink(__DIR__ . "/config.php");
rename(__DIR__ . "/configbis.php", __DIR__ . "/config.php");
