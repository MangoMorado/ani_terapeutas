<?php
// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once __DIR__ . '/../config/environment.php';
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../controllers/TerapeutaController.php';

    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');

    // Manejar preflight requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }

    $controller = new TerapeutaController();

    switch($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $result = $controller->index();
            echo json_encode($result);
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
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error interno del servidor',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
} 