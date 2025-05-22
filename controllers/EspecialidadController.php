<?php
require_once __DIR__ . '/../models/Especialidad.php';

class EspecialidadController {
    public function index() {
        $especialidad = new Especialidad();
        return $especialidad->getAll();
    }

    public function store($nombre) {
        $especialidad = new Especialidad();
        return $especialidad->create($nombre);
    }
} 