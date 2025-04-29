<?php
try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=localhost;dbname=tdr;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête pour récupérer tous les TDR
    $stmt = $pdo->query("SELECT * FROM ttdr");
    $tdrs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // S'il y a une erreur de connexion ou SQL
    if ($e->getCode() == 1049 || $e->getCode() == 2002) {
        // 1049 = Base de données inconnue, 2002 = Serveur non trouvé
        header("Location: erreur.php?type=connexion");
        exit;
    } else {
        header("Location: erreur.php?type=recuperation");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des TDR</title>
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>


<body>
<!-- ********************************** -->
<!-- Affichage du message apres suppression d'un tdr  -->
     
<?php if (isset($_GET['msg'])): ?>
    <div class="alert text-center 
        <?php echo ($_GET['msg'] == "suppression_reussie") ? "alert-success" : "alert-danger"; ?>">
        <?php
            if ($_GET['msg'] == "suppression_reussie") {
                echo "✅ Le TDR a été supprimé avec succès.";
            } elseif ($_GET['msg'] == "erreur_suppression") {
                echo "❌ Erreur lors de la suppression. Veuillez réessayer.";
            } elseif ($_GET['msg'] == "id_manquant") {
                echo "⚠️ Impossible de supprimer : ID non fourni.";
            }
        ?>
    </div>
<?php endif; ?>



<!-- **************** -->
<div class="container mt-5">
    <h2 class="mb-4">Liste des Termes De Références TDR </h2>

    <?php if ($tdrs): ?>
        <div class="table-responsive">
            <table class="table tdr-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Durée</th>
                        <th>Chef de mission</th>
                        <th>Membres</th>
                        <th>Itinéraire</th>
                        <th>Budget (FCFA)</th>
                        <th>Action</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tdrs as $tdr): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($tdr['idtdr']); ?></td>
                            <td><?php echo htmlspecialchars($tdr['dureeMission']); ?></td>
                            <td><?php echo htmlspecialchars($tdr['chefMission']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($tdr['membreMission'])); ?></td>
                            <td><?php echo htmlspecialchars($tdr['itineraire']); ?></td>
                            <td><?php echo number_format($tdr['budgetMission'], 0, ',', ' '); ?> FCFA</td>
                            <td>
                                <a href="modifier_tdr.php?id=<?php echo $tdr['idtdr']; ?>" class="btn btn-sm btn-warning">Modifier</a>
                                <a href="supprimer_tdr.php?id=<?php echo $tdr['idtdr']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression ?');">Supprimer</a>
                                <a href="detail_tdr.php?id=<?php echo $tdr['idtdr']; ?>" class="btn btn-info btn-sm">Voir détails</a>                          
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table> <br><br><br>
            <a href="dashboard.php" class="btn btn-danger px-4">  Retour  </a>
            <a class="btn btn-success" id="addTDRTab" data-bs-toggle="tab" href="ajout_tdr.php" role="tab" aria-controls="addTDR" aria-selected="true">Ajouter un TDR</a>
        
        </div>
    <?php else: ?>
        <div class="alert alert-info">Aucun TDR n’a encore été enregistré.</div>
    <?php endif; ?>
</div>
</body>
</html>
