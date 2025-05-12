<?php
$pdo = new PDO("mysql:host=localhost;dbname=tdrr;charset=utf8", "root", "");

// Récupération de l'ID via GET, avec vérification
$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $stmt = $pdo->prepare("SELECT
                                    t.*,
                                    v.matricule,
                                    v.marque,
                                    v.conducteur_titre,
                                    v.conducteur_nom,
                                    CONCAT(pc.nom, ' ', pc.prenom) AS chef_mission,
                                    GROUP_CONCAT(CONCAT(p.nom, ' ', p.prenom) SEPARATOR ', ') AS noms_membres,
                                    GROUP_CONCAT(p.titre SEPARATOR ', ') AS titres_membres
                                FROM
                                    ttdr t
                                LEFT JOIN 
                                    vehicules v ON t.idvehicule = v.idvehicule
                                LEFT JOIN 
                                    membres_tdr mt ON mt.idtdr = t.idtdr
                                LEFT JOIN
                                    personnel p ON mt.idpersonnel = p.idpersonnel
                                LEFT JOIN 
                                    personnel pc ON t.chefMission = pc.idpersonnel
                                WHERE
                                    t.idtdr = ?
                                GROUP BY
                                    t.idtdr
                                ORDER BY
                                    t.idtdr     
                             ");


        $stmt->execute([$id]);
        $tdr = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tdr) {
            // Si le TDR n'existe pas dans la base
            header("Location: erreur.php?type=inexistant");
            exit();
        }
    } catch (PDOException $e) {
        // En cas d'erreur lors de la requête
        header("Location: erreur.php?type=sql" . $e->getMessage());
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
    <link rel="stylesheet" href="../css/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style.css">
</head>

<style>
    .info-row {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 8px;
    }

    .label-span {
        width: 200px;
        font-weight: bold;
    }

    @media (max-width: 576px) {
        .info-row {
            flex-direction: column;
        }

        .label-span {
            width: 100%;
            margin-bottom: 4px;
        }
    }
</style>
<body>
<div class="container">
    <div id="details">
        <h2>Détails du TDR</h2>
        
        <div class="info-row"><span class="label-span">Titre de la mission    : </span><?php echo $tdr['titreMission']; ?></div>
        <div class="info-row"><span class="label-span">Objectif de la mission : </span><?php echo $tdr['objectifMission']; ?></div>
        <div class="info-row"><span class="label-span">Activité               : </span><?php echo $tdr['activite']; ?></div>
        <div class="info-row"><span class="label-span">Durée de la mission    : </span><?php echo $tdr['dureeMission']; ?> Jour(s)</div>
        <div class="info-row"><span class="label-span">Chef de mission        : </span><?php echo $tdr['chef_mission']; ?></div>
        <div class="info-row"><span class="label-span">Membres                : </span><?php echo nl2br($tdr['membres']??''); ?></div>
        <div class="info-row"><span class="label-span">Itinéraire             : </span><?php echo $tdr['itineraire']; ?></div>
        <div class="info-row"><span class="label-span">Budget estimé          : </span><?php echo number_format($tdr['budgetMission'], 2, ',', ' ') . " FCFA"; ?></div>
        <div class="info-row"><span class="label-span">Etape actuel           : </span><?php echo $tdr['etat_actuel']; ?></div>
        <div class="info-row"><span class="label-span">Conducteur affecté     : </span><?php echo $tdr['conducteur_nom']; ?></div>


        <div class="button-container">
            <a href="../modifier_tdr.php?id=<?php echo $tdr['idtdr']; ?>" class="btn-modifier">Modifier le TDR</a>
            <a href="liste_tdr_membre.php" class="btn-back">Retour à la liste</a>
            </div>

    </div>
</div>



</body>
</html>
