<?php
require_once __DIR__ . '/../controllers/AgendarController.php';

header('Content-Type: application/json');
$controller = new AgendarController();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            echo json_encode($controller->citaPorId($_GET['id']));
        } else if (isset($_GET['paciente_id'])) {
            if (isset($_GET['fecha'])) {
                // (Opcional) citas de un paciente en una fecha
                echo json_encode($controller->citasPorPaciente($_GET['paciente_id']));
            } else {
                echo json_encode($controller->citasPorPaciente($_GET['paciente_id']));
            }
        } else if (isset($_GET['fecha'])) {
            echo json_encode($controller->citasPorFecha($_GET['fecha']));
        } else {
            echo json_encode(['error' => 'Falta paciente_id o fecha']);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $paciente_id = $data['paciente_id'] ?? null;
        $terapeuta_id = $data['terapeuta_id'] ?? null;
        $fecha = $data['fecha'] ?? null;
        $hora_inicio = $data['hora_inicio'] ?? null;
        $hora_fin = $data['hora_fin'] ?? null;
        $secuencia_id = $data['secuencia_id'] ?? null;
        $orden_en_secuencia = $data['orden_en_secuencia'] ?? null;
        $result = $controller->agendar($paciente_id, $terapeuta_id, $fecha, $hora_inicio, $hora_fin, $secuencia_id, $orden_en_secuencia);
        echo json_encode(['success' => $result]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
} 