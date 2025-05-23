<?php
require_once __DIR__ . '/../models/Configuracion.php';

class ConfiguracionController {
    private $configuracion;

    public function __construct() {
        $this->configuracion = new Configuracion();
    }

    public function get($clave) {
        return $this->configuracion->get($clave);
    }

    public function set($clave, $valor) {
        return $this->configuracion->set($clave, $valor);
    }

    public function getAll() {
        return $this->configuracion->getAll();
    }
} 