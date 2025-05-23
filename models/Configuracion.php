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
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['valor'] : null;
    }

    public function set($clave, $valor) {
        $query = "INSERT INTO " . $this->table . " (clave, valor) VALUES (?, ?) 
                 ON DUPLICATE KEY UPDATE valor = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$clave, $valor, $valor]);
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $config = [];
        foreach ($result as $row) {
            $config[$row['clave']] = $row['valor'];
        }
        return $config;
    }
} 