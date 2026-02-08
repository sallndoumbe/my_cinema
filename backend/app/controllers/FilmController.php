<?php
namespace Controllers;

use Repositories\FilmRepository;
use Models\Film;

class FilmController {
    private $repo;

    public function __construct() {
        $this->repo = new FilmRepository();
    }

    public function liste() {
        echo json_encode($this->repo->trouverTout());
    }

    public function ajouter($donnees) {
        if (!is_array($donnees)) {
            http_response_code(400);
            echo json_encode(["error" => "Corps JSON invalide."]);
            return;
        }

        $titre = trim($donnees['titre'] ?? $donnees['title'] ?? '');
        $duree = $donnees['duree'] ?? $donnees['duration'] ?? null;
        $errors = [];

        if ($titre === '') {
            $errors['titre'] = "Le titre est requis.";
        }
        if (!is_numeric($duree)) {
            $errors['duree'] = "La durée doit être un nombre.";
        }
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(["error" => "Données invalides.", "fields" => $errors]);
            return;
        }

        $film = new Film([
            'titre' => $titre,
            'duree' => (int)$duree,
            'genre' => $donnees['genre'] ?? null,
            'description' => $donnees['description'] ?? null,
            'date_sortie' => $donnees['date_sortie'] ?? $donnees['release_date'] ?? null
        ]);

        if ($this->repo->creer($film)) {
            echo json_encode(["status" => "success"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erreur lors de la création du film."]);
        }
    }

    public function supprimer($id) {
        if ($this->repo->supprimer($id)) {
            echo json_encode(["status" => "deleted"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Impossible : film lié à des séances."]);
        }
    }
}