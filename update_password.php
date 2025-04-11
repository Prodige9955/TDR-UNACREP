<?php
require 'connexion.php';

$sql = "SELECT id, password FROM utilisateur";
$stmt = $conn->query($sql);
$users = $stmt->fetchAll();

foreach ($users as $user) {
    if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
        $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);
        $updateSql = "UPDATE utilisateur SET password = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->execute([$hashedPassword, $user['id']]);
        echo "Mot de passe mis à jour pour l'utilisateur ID " . $user['id'] . "<br>";
    }
}
?>
