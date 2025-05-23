<?php
require_once __DIR__ . '/../controllers/ConfiguracionController.php';

header('Content-Type: application/json');
$controller = new ConfiguracionController();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['clave'])) {
            echo json_encode(['valor' => $controller->get($_GET['clave'])]);
        } else {
            echo json_encode($controller->getAll());
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $clave = $data['clave'] ?? null;
        $valor = $data['valor'] ?? null;
        if (!$clave || !$valor) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos incompletos']);
            exit;
        }
        $result = $controller->set($clave, $valor);
        echo json_encode(['success' => $result]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
} 