<?php
require_once __DIR__ . '/../config/database.php';

class Terapeuta {
    private $conn;
    private $table = 'terapeutas';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        $query = "SELECT t.*, e.nombre as especialidad FROM " . $this->table . " t LEFT JOIN especialidades e ON t.especialidad_id = e.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($nombre, $especialidad_id) {
        $query = "INSERT INTO " . $this->table . " (nombre, especialidad_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$nombre, $especialidad_id]);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
} 