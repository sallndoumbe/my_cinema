<?php
namespace Repositories;

use Config\Database;
use Models\Salle;

class SalleRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function trouverToutesActives() {
        $stmt = $this->db->query("SELECT * FROM rooms WHERE is_deleted = 0");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function trouverParId($id) {
        $stmt = $this->db->prepare("SELECT * FROM rooms WHERE id = ? AND is_deleted = 0");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function creer(Salle $salle) {
        $stmt = $this->db->prepare("INSERT INTO rooms (name, capacity, type) VALUES (?, ?, ?)");
        return $stmt->execute([$salle->nom, $salle->capacite, $salle->type]);
    }

    public function suppressionDouce($id) {
        $stmt = $this->db->prepare("UPDATE rooms SET is_deleted = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}