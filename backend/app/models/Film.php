<?php
namespace Models;

/**
 * Représente un film au sein du système.
 */
class Film {
    public $id;
    public $titre;
    public $description;
    public $duree; // En minutes
    public $date_sortie;
    public $genre;

    public function __construct($donnees = []) {
        $this->id = $donnees['id'] ?? null;
        $this->titre = $donnees['titre'] ?? null;
        $this->description = $donnees['description'] ?? null;
        $this->duree = $donnees['duree'] ?? null;
        $this->date_sortie = $donnees['date_sortie'] ?? null;
        $this->genre = $donnees['genre'] ?? null;
    }
}