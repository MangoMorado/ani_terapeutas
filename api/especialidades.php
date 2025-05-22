<?php
require_once __DIR__ . '/../controllers/EspecialidadController.php';

header('Content-Type: application/json');
$controller = new EspecialidadController();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo json_encode($controller->index());
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $nombre = $data['nombre'] ?? '';
        $result = $controller->store($nombre);
        echo json_encode(['success' => $result]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
} 