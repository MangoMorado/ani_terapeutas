<?php
require_once __DIR__ . '/../controllers/PacienteController.php';

header('Content-Type: application/json');
$controller = new PacienteController();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo json_encode($controller->index());
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $nombre = $data['nombre'] ?? '';
        $email = $data['email'] ?? '';
        $result = $controller->store($nombre, $email);
        echo json_encode(['success' => $result]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
} 