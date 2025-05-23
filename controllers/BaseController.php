<?php
class BaseController {
    protected $db;

    public function __construct() {
        global $db;
        $this->db = $db;
    }

    protected function render($view, $data = []) {
        // Extraer los datos para que estén disponibles en la vista
        extract($data);
        
        // Incluir la vista
        require_once "views/{$view}.html";
    }

    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    protected function redirect($url) {
        header("Location: /ani_terapeutas{$url}");
        exit;
    }
}
?> 