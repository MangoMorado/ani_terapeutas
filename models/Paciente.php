<?php
require_once __DIR__ . '/../config/database.php';

class Paciente {
    private $conn;
    private $table = 'pacientes';

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

    public function create($nombre, $email) {
        $query = "INSERT INTO " . $this->table . " (nombre, email) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$nombre, $email]);
    }
} 