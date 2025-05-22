<?php
require_once __DIR__ . '/../models/Configuracion.php';

class ConfiguracionController {
    public function get($clave) {
        $config = new Configuracion();
        return $config->get($clave);
    }

    public function set($clave, $valor) {
        $config = new Configuracion();
        return $config->set($clave, $valor);
    }
} 