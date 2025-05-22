<?php
require_once __DIR__ . '/../models/Paciente.php';

class PacienteController {
    public function index() {
        $paciente = new Paciente();
        return $paciente->getAll();
    }

    public function store($nombre, $email) {
        $paciente = new Paciente();
        return $paciente->create($nombre, $email);
    }
} 