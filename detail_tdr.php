<?php
$pdo = new PDO("mysql:host=localhost;dbname=tdr;charset=utf8", "root", "");

// Récupération de l'ID via GET, avec vérification
$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM ttdr WHERE idtdr = ?");
        $stmt->execute([$id]);
        $tdr = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tdr) {
            // Si le TDR n'existe pas dans la base
            header("Location: erreur.php?type=inexistant");
            exit();
        }
    } catch (PDOException $e) {
        // En cas d'erreur lors de la requête
        header("Location: erreur.php?type=sql");
        exit();
    }
} else {
    // Si l'ID n'est pas fourni
    header("Location: erreur.php?type=vide");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du TDR</title>
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div id="details">
        <h2>Détails du TDR</h2>
        
        <div><span>ID du TDR : </span><?php echo $tdr['idtdr']; ?></div>
        <div><span>Durée de la mission : </span><?php echo $tdr['dureeMission']; ?></div>
        <div><span>Chef de mission : </span><?php echo $tdr['chefMission']; ?></div>
        <div><span>Membres : </span><?php echo nl2br($tdr['membreMission']); ?></div>
        <div><span>Itinéraire : </span><?php echo $tdr['itineraire']; ?></div>
        <div><span>Budget estimé : </span><?php echo number_format($tdr['budgetMission'], 2, ',', ' ') . " FCFA"; ?></div>
        
        <div class="button-container">
            <a href="liste_tdr.php" class="btn-back">Retour à la liste</a>
            <a href="modifier_tdr.php?id=<?php echo $tdr['idtdr']; ?>" class="btn-modifier">Modifier le TDR</a>
        </div>

    </div>
</div>



</body>
</html>
