<?php
require_once __DIR__ . '/../models/Secuencia.php';

class SecuenciaController {
    public function index() {
        $secuencia = new Secuencia();
        return $secuencia->getAll();
    }

    public function store($nombre, $descripcion) {
        $secuencia = new Secuencia();
        return $secuencia->create($nombre, $descripcion);
    }

    public function terapeutas($secuencia_id) {
        $secuencia = new Secuencia();
        return $secuencia->getTerapeutas($secuencia_id);
    }
} 