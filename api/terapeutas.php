<?php
require_once __DIR__ . '/../controllers/TerapeutaController.php';

header('Content-Type: application/json');
$controller = new TerapeutaController();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo json_encode($controller->index());
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $nombre = $data['nombre'] ?? '';
        $especialidad_id = $data['especialidad_id'] ?? null;
        $result = $controller->store($nombre, $especialidad_id);
        echo json_encode(['success' => $result]);
        break;
    case 'DELETE':
        parse_str(file_get_contents('php://input'), $data);
        $id = $_GET['id'] ?? $data['id'] ?? null;
        if ($id) {
            $result = $controller->destroy($id);
            echo json_encode(['success' => $result]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'ID requerido']);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
} 