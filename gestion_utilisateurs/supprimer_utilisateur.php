<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=tdrr', 'root', '');

// Vérification de l'ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Préparation et exécution de la suppression
    $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
    $stmt->execute([$id]);

    // Redirection après suppression
    header("Location: gestion_utilisateurs.php");
    exit;
} else {
    echo "ID utilisateur non spécifié.";
}
?>
