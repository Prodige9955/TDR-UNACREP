<?php 
session_start();
require '../connexion.php';
$message = '';

// Vérification du rôle : secretariat
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== 'secretariat') {
    header("Location: authentification.php");
    exit();
}

// fonction PHP pour gérer les transitions d'etat afin de derterminer le nouvel état
function getNextEtat($role, $decision) {
    if ($decision === 'valide') {
        switch ($role) {
            case 'responsable': return 'valide_responsable';
            case 'secretariat': return 'valide_secretariat';
            case 'directeur': return 'valide_directeur';
        }
    } else { // rejeté
        switch ($role) {
            case 'responsable': return 'rejete_responsable';
            case 'secretariat': return 'rejete_secretariat';
            case 'directeur': return 'tdr_annule';
        }
    }
    return null;
}


// Vérification de l'ID du TDR
if (isset($_GET['idtdr'])) {
    $idtdr = $_GET['idtdr'];

    $stmt = $conn->prepare("SELECT 
                                ttdr.*, 
                                vehicules.conducteur_nom,
                                CONCAT(pc.nom, ' ', pc.prenom) AS chef_mission,
                                GROUP_CONCAT(CONCAT( personnel.nom, ' ', personnel.prenom) SEPARATOR ', ') AS membres
                            FROM 
                                ttdr
                            LEFT JOIN 
                                vehicules ON ttdr.idvehicule = vehicules.idvehicule
                            LEFT JOIN 
                                membres_tdr ON ttdr.idtdr = membres_tdr.idtdr
                            LEFT JOIN 
                                personnel ON membres_tdr.idpersonnel = personnel.idpersonnel
                            LEFT JOIN 
                                    personnel pc ON ttdr.chefMission = pc.idpersonnel
                            WHERE 
                                ttdr.idtdr = ?
                            GROUP BY 
                                ttdr.idtdr
                            ");

    $stmt->execute([$idtdr]);
    $tdr = $stmt->fetch();

    if (!$tdr) {
        echo "TDR non trouvé.";
        exit();
    }
} else {
    echo "Aucun ID de TDR spécifié.";
    exit();
}

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["tdr_id"], $_POST["decision"])) {
    $tdr_id = $_POST["tdr_id"];
    $decision = $_POST["decision"];
    $commentaire = $_POST["commentaire"] ?? "";

    // Déterminer le nouvel état
    $role = $_SESSION["user"]["role"]; // doit être défini dans la session
    $etat = getNextEtat($role, $decision);

    // Insertion dans tdr_validations
    $stmt = $conn->prepare("
        INSERT INTO tdr_validations 
        (idtdr,validateur, decision, commentaire, titreMission, objectMission, activite) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $idtdr,
        'secretariat',
        $decision,
        $commentaire,
        $tdr['titreMission'],
        $tdr['objectifMission'],
        $tdr['activite']
    ]);

    // Mise à jour de l'état dans ttdr
    $stmt = $conn->prepare("UPDATE ttdr SET etat_actuel = ? WHERE idtdr = ?");
    $stmt->execute([$etat, $tdr_id]);

    // Message utilisateur
    $message = ($decision === "valide") 
        ? "✅ Le TDR a été validé par le sécrétariat." 
        : "❌ Le TDR a été rejeté par le sécrétariat.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du TDR - Secrétariat</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">


    <?php if (!empty($message)) : ?>
        <meta http-equiv="refresh" content="3;URL=validation_tdr_secretariat.php">
    <?php endif; ?>
    
    <style>
        body {
            background-color: #ffffff;
            color:rgb(60, 63, 60);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        h1 {
            color: #2e7d32;
            font-weight: bold;
        }
        p strong {
            color: #388e3c;
        }
        .btn-success {
            background-color: #2e7d32;
            border-color: #2e7d32;
        }
        .btn-success:hover {
            background-color: #1b5e20;
            border-color: #1b5e20;
        }
        .btn-danger {
            background-color: #c62828;
            border-color: #b71c1c;
        }
        .btn-danger:hover {
            background-color: #b71c1c;
        }
        .alert-info {
            background-color: #e8f5e9;
            color: #1b5e20;
            border-color: #c8e6c9;
        }
        label {
            color: #2e7d32;
        }
        .form-control:focus {
            border-color: #66bb6a;
            box-shadow: 0 0 0 0.2rem rgba(102, 187, 106, 0.25);
        }
        .col-md-8 {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(46, 125, 50, 0.1);
        }
        .button-container {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
        .btn-back {
            background-color: #f1f1f1;
            color: #2e7d32;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
        }
        .btn-back:hover {
            background-color: #c8e6c9;
        }

        p strong {
            color:rgba(17, 22, 14, 0.95);
        }
    </style>

</head>

<body class="bg-white d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-10 col-sm-12 mx-auto mt-4 p-4 shadow rounded" style="background-color: #fff;">
                
                <div class="container">
                    <h1 class="text-center">📋 Détails du TDR (Secrétariat)</h1><br>

                    <div class="row">
                        <div class="col-12" style="padding-left: 100px; padding-right: 30px; text-align: justify;">

                                <h5>Titre Mission : <?= htmlspecialchars($tdr['titreMission']) ?></h5>
                                <p><strong>Objectif :</strong> <?= nl2br(htmlspecialchars($tdr['objectifMission'])) ?></p>
                                <p><strong>Activité :</strong> <?= nl2br(htmlspecialchars($tdr['activite'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>
                                <p><strong>Durée :</strong> <?= htmlspecialchars($tdr['dureeMission']) ?> jours</p>
                                <p><strong>Chef de mission :</strong> <?= htmlspecialchars($tdr['chef_mission']) ?></p>
                                <p><strong>Membres :</strong> <?= nl2br(htmlspecialchars($tdr['membres']?? '')) ?></p>
                                <p><strong>Itinéraire :</strong> <?= nl2br(htmlspecialchars($tdr['itineraire'])) ?></p>
                                <p><strong>Frais mission :</strong> <?= htmlspecialchars($tdr['fraisMission']) ?> FCFA</p>
                                <p><strong>Carburant :</strong> <?= htmlspecialchars($tdr['carburant']) ?> FCFA</p>
                                <p><strong>Péage :</strong> <?= htmlspecialchars($tdr['peage']) ?> FCFA</p>
                                <p><strong>Autres frais :</strong> <?= htmlspecialchars($tdr['autresFrais']) ?> FCFA</p>
                                <p><strong>Budget total :</strong> <?= htmlspecialchars($tdr['budgetMission']) ?> FCFA</p>
                                <p><strong>État actuel :</strong> <?= htmlspecialchars($tdr['etat_actuel']) ?></p>
                                <p><strong>Conducteur affecté :</strong> <?= htmlspecialchars($tdr['conducteur_nom'] ?? '') ?></p>
                        </div>
                    </div>
                </div>


                <?php if (!empty($message)) : ?>
                    <div class="alert alert-info mt-3">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <input type="hidden" name="tdr_id" value="<?= $tdr['idtdr'] ?>">
                    <div class="mb-2">
                        <label for="commentaire">Commentaire :</label>
                        <textarea name="commentaire" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="button-container text-center mt-3">
                        <button name="decision" value="valide" class="btn btn-success me-2">✅ Valider</button>
                        <button name="decision" value="rejete" class="btn btn-danger me-2">❌ Rejeter</button>
                        <a href="validation_tdr_secretariat.php" class="btn btn-secondary">Retour à la liste</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Envoie une requête toutes les 10 minutes pour garder la session active pour garder la section active
        setInterval(function() {
            fetch('../keep_alive.php'); // le ".." remonte d’un dossier
        }, 600000); // 10 minutes
    </script>

</body>
</html>
