<?php
require_once __DIR__ . '/../config/database.php';

class Especialidad {
    private $conn;
    private $table = 'especialidades';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($nombre) {
        $query = "INSERT INTO " . $this->table . " (nombre) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$nombre]);
    }
} 