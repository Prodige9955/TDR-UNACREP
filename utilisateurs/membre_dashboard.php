<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== 'membre') {
    header("Location: authentification.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord - Membre</title>
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
        .dashboard-links a {
            display: block;
            margin: 15px auto;
            padding: 12px 20px;
            width: 70%;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            border-radius: 8px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .dashboard-links a:hover {
            background-color: #0056b3;
            color: white;
        }
    </style>
</head>
<body>

    <div class="dashboard-container text-center">
        <div class="dashboard-header">
            <h1>Bienvenue, <?= htmlspecialchars($_SESSION["user"]["prenom"]) ?> (Membre)</h1>
            <p>Vous pouvez soumettre un TDR ou suivre l’état de vos demandes.</p>
        </div>

        <div class="dashboard-links">
            <a href="../ajout_tdr.php">➕ Soumettre un nouveau TDR</a>
            <a href="../validation_tdr/liste_tdr_membre.php">📊 Suivre mes TDR</a>
        </div>
    </div>

    <script>
        // Garder la session active toutes les 10 minutes
        setInterval(function() {
            fetch('../keep_alive.php');
        }, 600000);
    </script>

</body>
</html>
