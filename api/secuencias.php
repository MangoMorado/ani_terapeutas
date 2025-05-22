<?php
require_once __DIR__ . '/../controllers/SecuenciaController.php';

header('Content-Type: application/json');
$controller = new SecuenciaController();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['terapeutas']) && isset($_GET['id'])) {
            echo json_encode($controller->terapeutas($_GET['id']));
        } else {
            echo json_encode($controller->index());
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $nombre = $data['nombre'] ?? '';
        $descripcion = $data['descripcion'] ?? '';
        $result = $controller->store($nombre, $descripcion);
        echo json_encode(['success' => $result]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
} 