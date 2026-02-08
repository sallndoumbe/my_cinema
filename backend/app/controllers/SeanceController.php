<?php
namespace Controllers;

use Repositories\SeanceRepository;
use Repositories\FilmRepository;
use Repositories\SalleRepository;
use Models\Seance;

class SeanceController {
    private $repo;
    private $filmRepo;
    private $salleRepo;

    public function __construct() {
        $this->repo = new SeanceRepository();
        $this->filmRepo = new FilmRepository();
        $this->salleRepo = new SalleRepository();
    }

    public function liste() {
        echo json_encode($this->repo->trouverToutAvecDetails());
    }

    public function planifier($donnees) {
        if (!is_array($donnees)) {
            http_response_code(400);
            echo json_encode(["error" => "Corps JSON invalide."]);
            return;
        }

        $filmId = $donnees['film_id'] ?? $donnees['movie_id'] ?? null;
        $salleId = $donnees['salle_id'] ?? $donnees['room_id'] ?? null;
        $debut = $donnees['debut_at'] ?? $donnees['start_at'] ?? null;
        $errors = [];

        if (!is_numeric($filmId)) {
            $errors['film_id'] = "Film requis.";
        }
        if (!is_numeric($salleId)) {
            $errors['salle_id'] = "Salle requise.";
        }
        if (!$debut) {
            $errors['debut_at'] = "La date de début est requise.";
        }
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(["error" => "Données invalides.", "fields" => $errors]);
            return;
        }

        if (strtotime($debut) === false) {
            http_response_code(400);
            echo json_encode(["error" => "Données invalides.", "fields" => ["debut_at" => "Format de date invalide."]]);
            return;
        }

        $film = $this->filmRepo->trouverParId((int)$filmId);
        if (!$film) {
            http_response_code(404);
            echo json_encode(["error" => "Film introuvable"]);
            return;
        }

        $salle = $this->salleRepo->trouverParId((int)$salleId);
        if (!$salle) {
            http_response_code(404);
            echo json_encode(["error" => "Salle introuvable"]);
            return;
        }

        $duree = (int)$film['duration'];
        $fin = date('Y-m-d H:i:s', strtotime($debut . " + $duree minutes"));

        if ($this->repo->verifierConflit((int)$salleId, $debut, $fin)) {
            http_response_code(400);
            echo json_encode(["error" => "Conflit d'horaire dans cette salle."]);
            return;
        }

        $seance = new Seance([
            'film_id' => (int)$filmId,
            'salle_id' => (int)$salleId,
            'debut_at' => $debut,
            'fin_at' => $fin
        ]);

        if ($this->repo->creer($seance)) {
            echo json_encode(["status" => "success"]);
        }
    }
}
