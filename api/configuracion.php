<?php
require_once __DIR__ . '/../controllers/ConfiguracionController.php';

header('Content-Type: application/json');
$controller = new ConfiguracionController();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $clave = $_GET['clave'] ?? null;
        if ($clave) {
            echo json_encode(['valor' => $controller->get($clave)]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Falta clave']);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $clave = $data['clave'] ?? null;
        $valor = $data['valor'] ?? null;
        if ($clave && $valor !== null) {
            $ok = $controller->set($clave, $valor);
            echo json_encode(['success' => $ok]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Faltan datos']);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
} 