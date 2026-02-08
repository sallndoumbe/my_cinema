<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}


spl_autoload_register(function ($classe) {
    $base = dirname(__DIR__) . '/app/';
    $fichier = $base . str_replace('\\', '/', $classe) . '.php';
    if (file_exists($fichier)) {
        require_once $fichier;
    }
});

$ressource = $_GET['resource'] ?? null;
$id = $_GET['id'] ?? null;
$methode = $_SERVER['REQUEST_METHOD'];

$corpsRequete = file_get_contents("php://input");
$donnees = json_decode($corpsRequete, true);


switch ($ressource) {
    case 'films':
        $controleur = new Controllers\FilmController();
        if ($methode === 'GET') {
            $controleur->liste();
        } elseif ($methode === 'POST') {
            $controleur->ajouter($donnees);
        } elseif ($methode === 'DELETE') {
            $controleur->supprimer($id);
        }
        break;

    case 'salles':
        $controleur = new Controllers\SalleController();
        if ($methode === 'GET') {
            $controleur->liste();
        } elseif ($methode === 'POST') {
            $controleur->ajouter($donnees);
        } elseif ($methode === 'DELETE') {
            $controleur->supprimer($id);
        }
        break;

    case 'seances':
        $controleur = new Controllers\SeanceController();
        if ($methode === 'GET') {
            $controleur->liste();
        } elseif ($methode === 'POST') {
            $controleur->planifier($donnees);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode([
            "erreur" => "Ressource introuvable",
            "message" => "La ressource '$ressource' n'est pas gérée par cette API."
        ]);
        break;
}