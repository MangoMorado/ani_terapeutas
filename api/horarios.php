<?php
require_once __DIR__ . '/../controllers/HorarioController.php';

header('Content-Type: application/json');
$controller = new HorarioController();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo json_encode($controller->index());
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $hora_inicio = $data['hora_inicio'] ?? '';
        $hora_fin = $data['hora_fin'] ?? '';
        $duracion = isset($data['duracion']) ? intval($data['duracion']) : 15;
        if (!$hora_inicio || !$hora_fin || $duracion < 5 || $duracion > 60) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos']);
            exit;
        }
        // Generar slots
        $slots = [];
        $ini = strtotime($hora_inicio);
        $fin = strtotime($hora_fin);
        while ($ini + $duracion * 60 <= $fin) {
            $slot_ini = date('H:i:s', $ini);
            $slot_fin = date('H:i:s', $ini + $duracion * 60);
            $slots[] = [$slot_ini, $slot_fin];
            $ini += $duracion * 60;
        }
        // Validar solapamientos
        $horariosExistentes = $controller->index();
        foreach ($slots as $slot) {
            foreach ($horariosExistentes as $h) {
                if (!($slot[1] <= $h['hora_inicio'] || $slot[0] >= $h['hora_fin'])) {
                    http_response_code(409);
                    echo json_encode(['error' => 'Conflicto: ya existe un slot entre ' . $h['hora_inicio'] . ' y ' . $h['hora_fin']]);
                    exit;
                }
            }
        }
        $ok = true;
        foreach ($slots as $slot) {
            $ok = $ok && $controller->store($slot[0], $slot[1]);
        }
        echo json_encode(['success' => $ok, 'slots' => $slots]);
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