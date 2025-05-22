<?php
require_once __DIR__ . '/../config/database.php';

class Secuencia {
    private $conn;
    private $table = 'secuencias';

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

    public function create($nombre, $descripcion) {
        $query = "INSERT INTO " . $this->table . " (nombre, descripcion) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$nombre, $descripcion]);
    }

    public function getTerapeutas($secuencia_id) {
        $query = "SELECT st.*, t.nombre as terapeuta_nombre FROM secuencia_terapeutas st JOIN terapeutas t ON st.terapeuta_id = t.id WHERE st.secuencia_id = ? ORDER BY st.orden ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$secuencia_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 