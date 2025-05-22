<?php
require_once __DIR__ . '/../models/Terapeuta.php';

class TerapeutaController {
    public function index() {
        $terapeuta = new Terapeuta();
        return $terapeuta->getAll();
    }

    public function store($nombre, $especialidad_id) {
        $terapeuta = new Terapeuta();
        return $terapeuta->create($nombre, $especialidad_id);
    }

    public function destroy($id) {
        $terapeuta = new Terapeuta();
        return $terapeuta->delete($id);
    }
} 