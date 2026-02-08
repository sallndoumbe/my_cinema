<?php
namespace Controllers;

use Repositories\SalleRepository;
use Models\Salle;

class SalleController {
    private $repo;

    public function __construct() {
        $this->repo = new SalleRepository();
    }

    public function liste() {
        $salles = $this->repo->trouverToutesActives();
        echo json_encode($salles);
    }

    public function ajouter($donnees) {
        if (!is_array($donnees)) {
            http_response_code(400);
            echo json_encode(["error" => "Corps JSON invalide."]);
            return;
        }

        $nom = trim($donnees['nom'] ?? $donnees['name'] ?? '');
        $capacite = $donnees['capacite'] ?? $donnees['capacity'] ?? null;
        $errors = [];

        if ($nom === '') {
            $errors['nom'] = "Le nom est requis.";
        }
        if (!is_numeric($capacite)) {
            $errors['capacite'] = "La capacité doit être un nombre.";
        }
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(["error" => "Données invalides.", "fields" => $errors]);
            return;
        }

        $salle = new Salle([
            'nom' => $nom,
            'capacite' => (int)$capacite,
            'type' => $donnees['type'] ?? 'Standard'
        ]);
        
        if ($this->repo->creer($salle)) {
            echo json_encode(["status" => "success", "message" => "Salle créée avec succès."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erreur lors de la création de la salle."]);
        }
    }

    
    public function supprimer($id) {
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "ID de salle manquant."]);
            return;
        }

        if ($this->repo->suppressionDouce($id)) {
            echo json_encode(["status" => "deleted", "message" => "La salle a été marquée comme supprimée."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erreur lors de la suppression de la salle."]);
        }
    }
}