<?php
require_once __DIR__ . '/../config/database.php';

class Cita {
    private $conn;
    private $table = 'citas';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getByPaciente($paciente_id) {
        $query = "SELECT c.*, p.nombre as paciente_nombre, p.email as paciente_email, t.nombre as terapeuta_nombre, e.nombre as especialidad_nombre FROM " . $this->table . " c JOIN pacientes p ON c.paciente_id = p.id JOIN terapeutas t ON c.terapeuta_id = t.id LEFT JOIN especialidades e ON t.especialidad_id = e.id WHERE c.paciente_id = ? ORDER BY c.fecha, c.hora_inicio";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$paciente_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($paciente_id, $terapeuta_id, $fecha, $hora_inicio, $hora_fin, $secuencia_id, $orden_en_secuencia) {
        $query = "INSERT INTO " . $this->table . " (paciente_id, terapeuta_id, fecha, hora_inicio, hora_fin, secuencia_id, orden_en_secuencia) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$paciente_id, $terapeuta_id, $fecha, $hora_inicio, $hora_fin, $secuencia_id, $orden_en_secuencia]);
    }

    public function getByTerapeutaYFecha($terapeuta_id, $fecha) {
        $query = "SELECT * FROM " . $this->table . " WHERE terapeuta_id = ? AND fecha = ? ORDER BY hora_inicio";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$terapeuta_id, $fecha]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT c.*, p.nombre as paciente_nombre, p.email as paciente_email, t.nombre as terapeuta_nombre, e.nombre as especialidad_nombre FROM " . $this->table . " c JOIN pacientes p ON c.paciente_id = p.id JOIN terapeutas t ON c.terapeuta_id = t.id LEFT JOIN especialidades e ON t.especialidad_id = e.id WHERE c.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByFecha($fecha) {
        $query = "SELECT c.*, p.nombre as paciente_nombre, p.email as paciente_email, t.nombre as terapeuta_nombre, e.nombre as especialidad_nombre FROM " . $this->table . " c JOIN pacientes p ON c.paciente_id = p.id JOIN terapeutas t ON c.terapeuta_id = t.id LEFT JOIN especialidades e ON t.especialidad_id = e.id WHERE c.fecha = ? ORDER BY c.hora_inicio";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$fecha]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 