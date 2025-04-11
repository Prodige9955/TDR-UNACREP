<?php
$pdo = new PDO("mysql:host=localhost;dbname=tdr;charset=utf8", "root", "");

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM ttdr WHERE idtdr = ?");
    $stmt->execute([$id]);
    $tdr = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    echo "Aucun ID fourni.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du TDR</title>
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        #details {
            margin-top: 50px;
            padding: 30px;
            background: #f9f9f9;
            border-radius: 10px;
        }
        #details h2 {
            margin-bottom: 30px;
        }
        #details div {
            margin-bottom: 15px;
            font-size: 16px;
        }
        #details span {
            font-weight: bold;
        }
        .button-container {
            margin-top: 30px;
        }
        .btn-back, .btn-modifier {
            margin-right: 15px;
            padding: 10px 20px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
        }
        .btn-back {
            background-color: #dc3545;
        }
        .btn-modifier {
            background-color: #198754;
        }

        @media print {
            /* Masquer tout sauf le contenu à imprimer */
            body * {
                visibility: hidden;
            }

            #details, #details * {
                visibility: visible;
            }

            /* Ajuster les marges pour l'impression */
            #details {
                margin: 0;
                padding: 0;
                background: white;
            }

            .button-container, .btn-back, .btn-modifier, .bouton-imprimer {
                display: none; /* Masquer les boutons et liens lors de l'impression */
            }
        }


    </style>
</head>
<body>
<div class="container">
    <div id="details">
        <h2>Détails du TDR</h2>
        
        <div><span>Numéro du TDR : </span><?php echo $tdr['idtdr']; ?></div>
        <div><span>Titre de la mission : </span><?php echo $tdr['titreMission']; ?></div>
        <div><span>Objectif de la mission : </span><?php echo nl2br($tdr['objectMission']); ?></div>
        <div><span>Référence plan de travail : </span><?php echo $tdr['refPlanTravail']; ?></div>
        <div><span>Activités : </span><?php echo nl2br($tdr['activite']); ?></div>
        <div><span>Résultat attendu : </span><?php echo nl2br($tdr['resultatAttendu']); ?></div>
        <div><span>Durée de la mission : </span><?php echo $tdr['dureeMission']; ?></div>
        <div><span>Chef de mission : </span><?php echo $tdr['chefMission']; ?></div>
        <div><span>Membres : </span><?php echo nl2br($tdr['membreMission']); ?></div>
        <div><span>Parties prenantes : </span><?php echo nl2br($tdr['PartiesPrenantes']); ?></div>
        <div><span>Itinéraire : </span><?php echo $tdr['itineraire']; ?></div>
        <div><span>Budget estimé : </span><?php echo number_format($tdr['budgetMission'], 2, ',', ' ') . " FCFA"; ?></div>
        
        <div class="button-container">
            <a href="liste_tdr.php" class="btn-back">Retour à la liste</a>
            <a href="modifier_tdr.php?id=<?php echo $tdr['idtdr']; ?>" class="btn-modifier">Modifier le TDR</a>
            <button class="bouton-imprimer" onclick="window.print()">🖨️Imprimer</button>
        </div>
    </div>
</div>

</body>
</html>
