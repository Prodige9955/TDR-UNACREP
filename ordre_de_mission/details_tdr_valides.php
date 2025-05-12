
<?php
session_start();
require '../connexion.php';

// Vérifie si l'utilisateur est bien du rôle 'secretariat'
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== 'secretariat') {
    header("Location: authentification.php");
    exit();
}

// Vérifie si un idtdr est passé
if (!isset($_GET['idtdr'])) {
    echo "Aucun TDR spécifié.";
    exit();
}

$idtdr = $_GET['idtdr'];

// Récupère les infos du TDR
$stmt = $conn->prepare("SELECT 
                                ttdr.*, 
                                vehicules.conducteur_nom,
                                GROUP_CONCAT(CONCAT( personnel.nom, ' ', personnel.prenom) SEPARATOR ', ') AS membres
                            FROM 
                                ttdr
                            LEFT JOIN 
                                vehicules ON ttdr.idvehicule = vehicules.idvehicule
                            LEFT JOIN 
                                membres_tdr ON ttdr.idtdr = membres_tdr.idtdr
                            LEFT JOIN 
                                personnel ON membres_tdr.idpersonnel = personnel.idpersonnel
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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Détails TDR Validé - Secrétariat</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">

</head>
<style>
       

        body {
            background-color: #ffffff; 
            color::rgb(60, 63, 60); 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        h1 {
            color: #2e7d32; /* Vert plus soutenu */
            font-weight: bold;
        }

        p strong {
            color: #388e3c;
        }

        textarea.form-control {
            border: 1px solid #2e7d32;
        }

        a.bouton-centre {
            display: inline-block; /* Permet l'affichage côte à côte */
            width: 250px;
            margin: 10px; /* Espace entre les boutons */
            text-align: center;
            font-size: 18px;
            display: inline-block;
            width: 250px;
            margin: 10px;
            text-align: center;
            font-size: 18px;
            padding-top: 10px;
            padding-bottom: 10px;
            line-height: 0.9;
            min-height: 55px;
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

        p strong {
            color:rgba(17, 22, 14, 0.95);
        }
        

</style>

<body class="bg-white d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-10 col-sm-12 mx-auto mt-4 p-4 shadow rounded" style="background-color: #fff;">

            <div class="container">
                <h1 class="text-center">📋 Détails du TDR (Direction)</h1><br>

                <div class="row">
                    <div class="col-12" style="padding-left: 100px; padding-right: 30px; text-align: justify;">

                        <h5>Titre Mission : <?= htmlspecialchars($tdr['titreMission']) ?></h5>
                        <p><strong>Objectif :</strong> <?= nl2br(htmlspecialchars($tdr['objectifMission'])) ?></p>
                        <p><strong>Activité :</strong> <?= nl2br(htmlspecialchars($tdr['activite'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>
                        <p><strong>Durée :</strong> <?= htmlspecialchars($tdr['dureeMission']) ?> jours</p>
                        <p><strong>Chef de mission :</strong> <?= htmlspecialchars($tdr['chefMission']) ?></p>
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

            <div class="button-container text-center mt-3">
                <a href="generer_ordre_mission.php?idtdr=<?= $tdr['idtdr'] ?>" class=" btn btn-success bouton-centre">
                    📝 Générer l'ordre de mission
                </a>
                <a href="tdr_valides.php" class="btn btn-secondary bouton-centre"> ↩ Retour à la liste</a>
            </div>

    <script>
        // Envoie une requête toutes les 10 minutes pour garder la session active pour garder la section active
        setInterval(function() {
            fetch('../keep_alive.php'); // le ".." remonte d’un dossier
        }, 600000); // 10 minutes
    </script>
</body>
</html>
