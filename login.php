<!-- Fichier de redirirection vers Keycloak -->

<?php
session_start();
require 'vendor/autoload.php';

use Stevenmaguire\OAuth2\Client\Provider\Keycloak;

$provider = new Keycloak([
    'authServerUrl' => 'http://localhost:8080', // <-- corrigé ici
    'realm'         => 'tdr',
    'clientId'      => 'tdr',
    'clientSecret'  => 'ktfW0JnCo2l85O3GCYqo4UMEHLbbt9Mw',
    'redirectUri'   => 'http://localhost/TDR/callback.php',
]);

// Redirige vers Keycloak pour authentification
$authUrl = $provider->getAuthorizationUrl();
$_SESSION['oauth2state'] = $provider->getState();
header('Location: ' . $authUrl);
exit;