<?php
// Connexion à la base de données
require 'connexion.php';
// $host = "localhost";
// $dbname = "tdr";
// $username = "root";
// $password = "";

// try {
//     $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Pour voir les erreurs SQL

    // Connexion à la base de données
    $host = "localhost";
    $dbname = "tdr";
    $username = "root";
    $password = "";
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        // Vérifie si l'ID a été passé en GET
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $idtdr = $_GET['id'];
    
            // Requête de suppression
            $stmt = $pdo->prepare("DELETE FROM ttdr WHERE idtdr = :idtdr");
            $stmt->bindParam(':idtdr', $idtdr);
    
            if ($stmt->execute()) {
                // Redirection vers la liste après suppression
                header("Location: liste_tdr.php?msg=suppression_reussie");
                exit();
            } else {
                echo "Erreur lors de la suppression.";
            }
        } else {
            echo "ID manquant.";
        }
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
    ?>
    
