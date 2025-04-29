
<?php
// Récupérer le type d'erreur passé en URL
$typeErreur = $_GET['type'] ?? 'inconnu';

$message = "";

switch ($typeErreur) {
    case 'doublon':
        $message = "Erreur : Cet identifiant TDR existe déjà.";
        break;
    case 'vide':
        $message = "Erreur : Tous les champs obligatoires doivent être remplis.";
        break;
    case 'connexion':
        $message = "Erreur : Impossible de se connecter à la base de données.";
        break;
    case 'sql':
        $message = "Erreur : Problème lors de l'exécution de la requête SQL.";
        break;
    case 'recuperation':
      $message = "Erreur : Problème lors de la récuperation des données de la BDD.";
      break; 
      // erreur au niveau du fichier modifier_tdr.php
    case 'inexistant':
      $message = "Erreur : TDR introuvable.";
      break;
    case 'update':
        $message = "Erreur : échec de la mise à jour du TDR.";
        break;
      // **********************************   
      
      // erreur au niveau du fichier modifier_tdr.php

      case 'suppression':
        $message = "Une erreur est survenue lors de la suppression du TDR.";
        break;
      // **********************************   
    default:
        $message = "Erreur inconnue.";
        
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Erreur</title>
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">
    <style>
    body {
      background: linear-gradient(to right, #00c853, #b2ff59);
      color: white;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      height: 100vh;
      margin: 0;
      text-align: center;
    }
    h1 {
      font-size: 4rem;
      margin: 0;
      animation: bounce 2s infinite;
    }
    h2 {
      margin: 20px 0 10px;
      font-size: 2rem;
    }
    p {
      font-size: 1.2rem;
      margin-bottom: 30px;
    }
    a {
      padding: 12px 25px;
      background: white;
      color: #00c853;
      border-radius: 30px;
      text-decoration: none;
      font-weight: bold;
      transition: 0.3s;
    }
    a:hover {
      background: #b2ff59;
      color: white;
    }
    @keyframes bounce {
      0%, 100% {
        transform: translateY(0);
      }
      50% {
        transform: translateY(-20px);
      }
    }
  </style>
</head>
<body>
<div class="erreur-box">
    <h1>Une erreur est survenue !!</h1>
    <p><?php echo htmlspecialchars($message); ?></p>
    <a href="dashboard.php" class="btn btn-danger mt-3">Retour à l'accueil</a>
</div>
</body>
</html>

<!-- La page que vous cherchez est momentanément insdisponible. Nous nous chargeons de régler cela. -->
