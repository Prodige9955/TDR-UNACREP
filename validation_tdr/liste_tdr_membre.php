<?php
try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=localhost;dbname=tdrr;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête pour récupérer tous les TDR
    $stmt = $pdo->query("SELECT 
                            t.*,
                            CONCAT(pc.nom, ' ', pc.prenom) AS chef_mission,
                            GROUP_CONCAT(CONCAT(p.nom, ' ', p.prenom) SEPARATOR ', ') AS noms_membres,
                            GROUP_CONCAT(p.titre SEPARATOR ',') AS titres_membres
                        FROM 
                            ttdr t
                        LEFT JOIN membres_tdr mt ON mt.idtdr = t.idtdr
                        LEFT JOIN personnel p ON mt.idpersonnel = p.idpersonnel
                        LEFT JOIN 
                                personnel pc ON t.chefMission = pc.idpersonnel
                        WHERE etat_actuel='soumis'
                        GROUP BY t.idtdr
                        ORDER BY t.idtdr;
                        ");
                        

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
    <link rel="stylesheet" href="../css/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style.css">
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


<style>
    .btn-retour {
        display: block;
        width: 200px;
        /* margin: 00px auto; */
        padding: 10px;
        background-color: #d9534f;
        color: white;
        border: none;
        font-size: 18px;
        text-align: center;
        text-decoration: none;
        border-radius: 5px;
    }
    .btn-retour:hover {
        background-color: #662422 !important;
    }

    .btn-ajout {
        display: block;
        width: 200px;
        /* margin: 00px auto; */
        padding: 10px;
        background-color: #2ecc71;
        color: white;
        border: none;
        font-size: 18px;
        text-align: center;
        text-decoration: none;
        border-radius: 5px;
    }
    .btn-ajout:hover {
        background-color: #218869 !important;
    }


    /* bouton modifier, couleur de fond au pointage */
    .btn-warning:hover {
        background-color: #a77530 !important;
    }

    /* bouton voir details, couleur de fond au pointage */
    .btn-sm:hover {
        background-color:rgb(77, 78, 146); !important;
    }
    .btn-info{
        background-color:rgb(133, 135, 247);
    }
            /* Annuler les soumignement de tous les boutons lien */
        a.btn{
        text-decoration: none !important;
    }


</style>

<!-- **************** -->
<div class="container mt-5">
    <h2 class="mb-4">Liste des Termes De Références TDR </h2>

    <?php if ($tdrs): ?>
        <div class="table-responsive">
            <table class="table tdr-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre de la mission</th>
                        <th>Durée</th>
                        <th>Chef de mission</th>
                        <th>Membres</th>
                        <th>Itinéraire</th>
                        <th>Budget (FCFA)</th>
                        <th>Etape actuelle<th>Action</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tdrs as $tdr): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($tdr['idtdr']); ?></td>
                            <td><?php echo htmlspecialchars($tdr['titreMission']); ?></td>
                            <td><?php echo htmlspecialchars($tdr['dureeMission']); ?> Jour(s)</td>
                            <td><?php echo htmlspecialchars($tdr['chef_mission']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($tdr['noms_membres']?? ' ')); ?></td>
                            <td><?php echo htmlspecialchars($tdr['itineraire']); ?></td>
                            <td><?php echo number_format($tdr['budgetMission'], 0, ',', ' '); ?> FCFA</td>
                            <td><?php echo htmlspecialchars($tdr['etat_actuel']); ?></td>

                            <td>
                                <a href="details_tdr_membre.php?id=<?php echo $tdr['idtdr']; ?>" class="btn btn-info btn-sm">Voir détails</a>                          
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table> <br><br><br>
           <div class="bouton-container">
                <a href="dashboard.php" class="btn-retour">  Retour  </a>
                <a class="btn-ajout" id="addTDRTab" data-bs-toggle="tab" href="../ajout_tdr.php" role="tab" aria-controls="addTDR" aria-selected="true">Ajouter un TDR</a>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Aucun TDR n’a encore été enregistré.</div>
    <?php endif; ?>
</div>
</body>
</html>
