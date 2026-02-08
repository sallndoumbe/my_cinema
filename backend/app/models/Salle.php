<?php
namespace Models;

/**
 * Représente une salle de projection du cinéma.
 */
class Salle {
    public $id;
    public $nom;
    public $capacite;
    public $type; // Standard, IMAX, 3D, etc.
    public $est_supprime; // Pour le soft delete

    public function __construct($donnees = []) {
        $this->id = $donnees['id'] ?? null;
        $this->nom = $donnees['nom'] ?? null;
        $this->capacite = $donnees['capacite'] ?? null;
        $this->type = $donnees['type'] ?? 'Standard';
        $this->est_supprime = $donnees['est_supprime'] ?? 0;
    }
}