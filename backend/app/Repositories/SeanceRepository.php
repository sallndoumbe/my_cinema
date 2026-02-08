<?php
namespace Repositories;

use Config\Database;
use Models\Seance;

class SeanceRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function trouverToutAvecDetails() {
        $sql = "SELECT s.*, m.title as titre_film, r.name as nom_salle 
                FROM sessions s 
                JOIN movies m ON s.movie_id = m.id 
                JOIN rooms r ON s.room_id = r.id 
                ORDER BY s.start_at ASC";
        return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function verifierConflit($salle_id, $debut, $fin) {
        $sql = "SELECT COUNT(*) FROM sessions 
                WHERE room_id = ? 
                AND NOT (? >= end_at OR ? <= start_at)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$salle_id, $debut, $fin]);
        return $stmt->fetchColumn() > 0;
    }

    public function creer(Seance $seance) {
        $stmt = $this->db->prepare("INSERT INTO sessions (movie_id, room_id, start_at, end_at) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$seance->film_id, $seance->salle_id, $seance->debut_at, $seance->fin_at]);
    }
}