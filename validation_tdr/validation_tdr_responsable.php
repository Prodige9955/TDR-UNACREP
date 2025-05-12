<?php
session_start();
require '../connexion.php';

if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== 'responsable') {
    header("Location: ../authentification.php");
    exit();
}

$stmt = $conn->prepare("SELECT 
                            t.*,
                            GROUP_CONCAT(CONCAT(p.nom, ' ', p.prenom) SEPARATOR ', ') AS noms_membres,
                            GROUP_CONCAT(p.titre SEPARATOR ',') AS titres_membres
                        FROM 
                            ttdr t
                        LEFT JOIN membres_tdr mt ON mt.idtdr = t.idtdr
                        LEFT JOIN personnel p ON mt.idpersonnel = p.idpersonnel
                        WHERE etat_actuel='soumis'
                        GROUP BY t.idtdr
                        ORDER BY t.idtdr;
                        ");

$stmt->execute();
$tdrs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des TDR soumis - Responsable</title>
    <link rel="stylesheet" href="../css/bootstrap/css/bootstrap.min.css">
    
    <style>
    .tdr-card {
        position: relative;
        border: 1px solid #dee2e6;
        border-left: 6px solid #007bff;
        padding: 15px 15px 35px 15px; /* Réduction des espacements */
        border-radius: 10px;
        margin-bottom: 15px; /* Espacement entre les cartes réduit */
        background-color: #fdfdfd;
        transition: box-shadow 0.3s ease;
        min-height: 160px; /* Réduction de la hauteur minimale */
    }

    .tdr-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.08);
    }

    .tdr-title {
        font-size: 18px; /* Réduction de la taille du titre */
        font-weight: 600;
        color: #343a40;
    }

    .tdr-desc {
        font-size: 14px; /* Réduction de la taille de la description */
        color: #555;
        margin-top: 5px;
    }

    .btn-view {
        position: absolute;
        bottom: 10px;
        right: 10px;
        padding: 6px 12px; /* Réduction du padding */
        font-size: 13px; /* Taille du texte réduite */
        border-radius: 6px;
        background-color: #007bff;
        color: white;
        border: none;
    }

    .btn-view:hover {
        background-color: #0056b3;
        color: #fff;
    }

    .no-tdr {
        text-align: center;
        font-size: 16px; /* Taille du texte ajustée */
        color: #888;
        margin-top: 50px;
    }
</style>

</head>
<body>

    <div class="container-custom">
        <div class="header-title">📄 TDR en attente de validation</div>

        <?php if (empty($tdrs)): ?>
            <div class="no-tdr">Aucun TDR soumis en attente.</div>
        <?php else: ?>
            <?php foreach ($tdrs as $tdr): ?>
                <div class="tdr-card">
                    <div class="tdr-title"><?= htmlspecialchars($tdr['titreMission']) ?></div>
                    <div class="tdr-desc"><?= htmlspecialchars(substr($tdr['objectifMission'], 0, 250)) ?>...</div>
                    <a href="details_tdr_responsable.php?idtdr=<?= $tdr['idtdr'] ?>" class="btn btn-view">
                        👁 Voir les détails
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

    <script>
        // Maintenir la session active toutes les 10 minutes
        setInterval(function() {
            fetch('../keep_alive.php');
        }, 600000); // 10 minutes
    </script>

</body>
</html>
