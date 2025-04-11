<?php
session_start();
session_destroy();
header("Location: authentification.php"); // Redirige vers la page de connexion
exit();
?>
