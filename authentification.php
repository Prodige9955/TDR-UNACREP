<?php

session_start();
require 'connexion.php'; // Fichier de connexion à la base de données

$messageErreur = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["nom"]) || empty($_POST["password"])) {
        $messageErreur = "Veuillez remplir tous les champs.";
    } else {
        $nom = $_POST["nom"];
        $password = $_POST["password"];

        // Requête pour récupérer l'utilisateur depuis la base de données
        $sql = "SELECT * FROM utilisateur WHERE nom = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nom]);
        $user = $stmt->fetch();

        if ($user) {
            // Vérification du mot de passe
            if (password_verify($password, $user['password'])) {
                $_SESSION["user"] = [
                    "id" => $user["id"],
                    "nom" => $user["nom"],
                    "prenom" => $user["prenom"],
                    "service" => $user["service"]
                ];
                header("Location: dashboard.php");
                exit();
            } else {
                $messageErreur = "Mot de passe incorrect.";
            }
        } else {
            $messageErreur = "Utilisateur introuvable.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'Authentification</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">

</head>
<body>
    <div class="login-container">
        <h2>Connexion</h2>
    </div>  
        <!-- Formulaire d'authentification avec PHP -->
        <form method="POST" class="form-container">
        <div class="mb-3">
            <label for="nom">Nom :</label>
            <input type="text" name="nom" class="form-control" placeholder="Nom" required>
        </div>
        <div class="mb-3">
            <label for="password">Mot de passe :</label>
            <input type="password" name="password" class="form-control" placeholder="Mot de passe" required>
        </div><br>
            <button type="submit" class="btn-vert">Se connecter</button> <br><br>
        <p> Nouveau compte ? <a href="inscription.php">S'inscrire</a></p>

        </form>
        
        <!-- Affichage des erreurs -->
        <?php 
        if (!empty($messageErreur)) {
            echo "<p class='error'>" . htmlspecialchars($messageErreur) . "</p>";
        }
        ?>
    </div>
</body>
</html>
