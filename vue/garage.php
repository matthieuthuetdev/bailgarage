<?php
$message = $_SESSION["message"];
$_SESSION["message"] = "";
?>
<?php echo $message ?>