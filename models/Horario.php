<?php
require_once __DIR__ . '/../config/database.php';

class Horario {
    private $conn;
    private $table = 'horarios';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY hora_inicio ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($hora_inicio, $hora_fin) {
        $query = "INSERT INTO " . $this->table . " (hora_inicio, hora_fin) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$hora_inicio, $hora_fin]);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
} 