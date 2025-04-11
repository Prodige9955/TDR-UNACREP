<?php
require 'connexion.php'; // Connexion à la base de données

$messageErreur = "";
$messageSucces = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["nom"]) || empty($_POST["prenom"]) || empty($_POST["service"]) || empty($_POST["password"])) {
        $messageErreur = "Veuillez remplir tous les champs.";
    } else {
        $nom = $_POST["nom"];
        $prenom = $_POST["prenom"];
        $service = $_POST["service"];
        $password = $_POST["password"];

        // Hachage du mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insertion dans la base de données
        $sql = "INSERT INTO utilisateur (nom, prenom, service, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt->execute([$nom, $prenom, $service, $hashedPassword])) {
            $messageSucces = "✅ Inscription réussie ! Vous pouvez maintenant vous connecter.";
            header("Location: authentification.php");
            // header("Location: authentification.php"); // Redirige vers la page de connexion

        } else {
            $messageErreur = "❌ Une erreur s'est produite lors de l'inscription.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <!-- <link rel="stylesheet" href="style.css"> -->
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
        <div class="login-container">
            <h2>Inscription</h2>
        </div> 
    <form method="POST" class="form-container">
        <div class="mb-3">
            <label for="nom">Nom :</label>
            <input type="text" name="nom" class="form-control" placeholder="Nom" required>
        </div>
        <div class="mb-3">
            <label for="prenom">Prénom :</label>
            <input type="text" name="prenom" class="form-control" placeholder="Prénom" required>
        </div>
        <div class="mb-3">
            <label for="service">Service :</label>
            <input type="text" name="service" class="form-control" placeholder="Service" required>
        </div>
        <div class="mb-3">
            <label for="password">Mot de passe :</label>
            <input type="password" name="password" class="form-control" placeholder="Mot de passe" required>
        </div>
        <button type="submit" class="btn-vert">S'inscrire</button><br> <br>

        <!-- Affichage des messages -->
        <?php 
        if (!empty($messageErreur)) {
            echo "<p class='error'>" . htmlspecialchars($messageErreur) . "</p>";
        }
        if (!empty($messageSucces)) {
            echo "<p class='success'>" . htmlspecialchars($messageSucces) . "</p>";
        }
        ?>
        
        <p>Déjà un compte ? <br><br> <a href="authentification.php">Se connecter</a></p>
    </div>
    </form>

</body>
</html>
