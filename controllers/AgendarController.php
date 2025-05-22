<?php
require_once __DIR__ . '/../models/Cita.php';

class AgendarController {
    public function citasPorPaciente($paciente_id) {
        $cita = new Cita();
        return $cita->getByPaciente($paciente_id);
    }

    public function citasPorFecha($fecha) {
        $cita = new Cita();
        return $cita->getByFecha($fecha);
    }

    public function agendar($paciente_id, $terapeuta_id, $fecha, $hora_inicio, $hora_fin, $secuencia_id, $orden_en_secuencia) {
        $cita = new Cita();
        return $cita->create($paciente_id, $terapeuta_id, $fecha, $hora_inicio, $hora_fin, $secuencia_id, $orden_en_secuencia);
    }

    public function citaPorId($id) {
        $cita = new Cita();
        return $cita->getById($id);
    }
} 