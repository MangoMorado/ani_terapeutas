<?php
// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once __DIR__ . '/../config/environment.php';
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../controllers/EspecialidadController.php';

    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');

    // Manejar preflight requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }

    $controller = new EspecialidadController();

    switch($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $result = $controller->index();
            echo json_encode($result);
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
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error interno del servidor',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
} 