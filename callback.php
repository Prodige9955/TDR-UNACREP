<!-- Fichier de  traitement de la réponse de Keycloak -->


<?php
session_start();
require 'vendor/autoload.php';

use League\OAuth2\Client\Provider\Keycloak;

$provider = new Keycloak([
    // 'authServerUrl' => 'http://localhost:8080/realms/tdr', erreur
    'authServerUrl' => 'http://localhost:8080',
    'realm'         => 'tdr',
    'clientId'      => 'tdr',
    'clientSecret'  => 'ktfW0JnCo2l85O3GCYqo4UMEHLbbt9Mw',
    'redirectUri'   => 'http://localhost/TDR/callback.php',
]);

// Vérifie l'état
if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('État invalide');
}

// Récupère le token
$token = $provider->getAccessToken('authorization_code', [
    'code' => $_GET['code']
]);

// Récupère les infos de l'utilisateur
$user = $provider->getResourceOwner($token);
$userInfo = $user->toArray();

echo "<h1>Bienvenue " . htmlspecialchars($userInfo['preferred_username']) . "</h1>";
echo "<pre>";
print_r($userInfo);
echo "</pre>";
