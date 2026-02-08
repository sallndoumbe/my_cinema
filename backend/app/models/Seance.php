<?php
namespace Models;

/**
 * Représente une séance de cinéma (projection d'un film dans une salle).
 */
class Seance {
    public $id;
    public $film_id;
    public $salle_id;
    public $debut_at;
    public $fin_at;

    public function __construct($donnees = []) {
        $this->id = $donnees['id'] ?? null;
        $this->film_id = $donnees['film_id'] ?? null;
        $this->salle_id = $donnees['salle_id'] ?? null;
        $this->debut_at = $donnees['debut_at'] ?? null;
        $this->fin_at = $donnees['fin_at'] ?? null;
    }
}