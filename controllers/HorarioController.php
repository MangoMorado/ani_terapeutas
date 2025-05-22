<?php
require_once __DIR__ . '/../models/Horario.php';

class HorarioController {
    public function index() {
        $horario = new Horario();
        return $horario->getAll();
    }

    public function store($hora_inicio, $hora_fin) {
        $horario = new Horario();
        return $horario->create($hora_inicio, $hora_fin);
    }

    public function destroy($id) {
        $horario = new Horario();
        return $horario->delete($id);
    }
} 