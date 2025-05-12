<?php

$servername="localhost";
$username="root";
$password="";
$dbname="tdrr";



try {
 $conn=new PDO('mysql: host='.$servername .' ;dbname=' .$dbname ,$username, $password);
 $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//  echo " La connexion a été bien établie ";
} 

catch (PDOException $e) {
    echo " La connexion a echoué : " .$e->getMessage();
}
 
?>
