<?php
namespace Repositories;

use Config\Database;
use Models\Film;

class FilmRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function trouverTout() {
        $stmt = $this->db->query("SELECT * FROM movies");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function trouverParId($id) {
        $stmt = $this->db->prepare("SELECT * FROM movies WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function creer(Film $film) {
        $stmt = $this->db->prepare("INSERT INTO movies (title, duration, genre, description) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$film->titre, $film->duree, $film->genre, $film->description]);
    }

    public function supprimer($id) {
        $check = $this->db->prepare("SELECT COUNT(*) FROM sessions WHERE movie_id = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) return false;

        $stmt = $this->db->prepare("DELETE FROM movies WHERE id = ?");
        return $stmt->execute([$id]);
    }
}