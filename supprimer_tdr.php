<?php
// Connexion à la base de données
$host = "localhost";
$dbname = "tdr";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifie si l'ID a été passé en GET et est valide
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $idtdr = $_GET['id'];

        // Vérifier si le TDR existe avant de tenter de le supprimer
        $stmt = $pdo->prepare("SELECT * FROM ttdr WHERE idtdr = :idtdr");
        $stmt->bindParam(':idtdr', $idtdr);
        $stmt->execute();
        $tdr = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($tdr) {
            // Requête de suppression
            $stmt = $pdo->prepare("DELETE FROM ttdr WHERE idtdr = :idtdr");
            $stmt->bindParam(':idtdr', $idtdr);

            if ($stmt->execute()) {
                // Redirection vers la liste après suppression avec un message de succès
                header("Location: liste_tdr.php?msg=suppression_reussie");
                exit();
            } else {
                // Redirection en cas d'erreur de suppression
                header("Location: erreur.php?type=suppression");
                exit();
            }
        } else {
            // Si le TDR n'existe pas dans la base
            header("Location: erreur.php?type=inexistant");
            exit();
        }
    } else {
        // Si l'ID est manquant
        header("Location: erreur.php?type=vide");
        exit();
    }
} catch (PDOException $e) {
    // En cas d'erreur de connexion à la base
    header("Location: erreur.php?type=sql");
    exit();
}
?>
