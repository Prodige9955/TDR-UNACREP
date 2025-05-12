<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== 'directeur') {
    header("Location: authentification.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord - Directeur</title>
    <link rel="stylesheet" href="../css/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .dashboard-container {
            max-width: 800px;
            margin: 60px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .dashboard-header {
            margin-bottom: 20px;
        }
        .dashboard-header h1 {
            font-size: 28px;
            font-weight: bold;
        }
        .dashboard-header p {
            font-size: 16px;
            color: #555;
        }
        .dashboard-link {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 20px;
            background-color: #28a745;
            color: white;
            font-size: 16px;
            border-radius: 8px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .dashboard-link:hover {
            background-color: #218838;
            text-decoration: none;
            color: white;
        }
    </style>
</head>
<body>

    <div class="dashboard-container text-center">
        <div class="dashboard-header">
            <h1>Bienvenue, <?= htmlspecialchars($_SESSION["user"]["prenom"]) ?> (Directeur Exécutif)</h1>
            <p>Vous pouvez valider ou annuler les TDR finaux. </p>
        </div>

        <a class="dashboard-link" href="/TDR/validation_tdr/validation_tdr_directeur.php">
            ✅ Examiner les TDR en attente
        </a>
    </div>

    <script>
        // Garder la session active toutes les 10 minutes
        setInterval(function() {
            fetch('../keep_alive.php');
        }, 600000);
    </script>

</body>
</html>
