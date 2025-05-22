<?php
require_once __DIR__ . '/../config/database.php';

class Configuracion {
    private $conn;
    private $table = 'configuracion';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function get($clave) {
        $query = "SELECT valor FROM " . $this->table . " WHERE clave = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$clave]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['valor'] : null;
    }

    public function set($clave, $valor) {
        $query = "REPLACE INTO " . $this->table . " (clave, valor) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$clave, $valor]);
    }
} 