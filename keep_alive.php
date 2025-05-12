
<?php
session_start();
// Ce petit changement suffit à dire à PHP "Hey, la session est utilisée !"
$_SESSION['last_keep_alive'] = time(); 
http_response_code(200); // Code OK