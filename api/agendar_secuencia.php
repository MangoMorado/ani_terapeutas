<?php
require_once __DIR__ . '/../models/Cita.php';
require_once __DIR__ . '/../models/Horario.php';
require_once __DIR__ . '/../models/Terapeuta.php';
require_once __DIR__ . '/../models/Paciente.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $paciente_id = $data['paciente_id'] ?? null;
    $fecha = $data['fecha'] ?? null;
    $pasos = $data['pasos'] ?? [];
    $accion = $data['accion'] ?? 'sugerir'; // 'sugerir' o 'agendar'
    $opcion = $data['opcion'] ?? null; // índice de la opción seleccionada

    if (!$paciente_id || !$fecha || !is_array($pasos) || count($pasos) < 1) {
        http_response_code(400);
        echo json_encode(['error' => 'Datos incompletos']);
        exit;
    }

    // 1. Obtener horarios ordenados
    $horarioModel = new Horario();
    $horarios = $horarioModel->getAll();
    if (count($horarios) < count($pasos)) {
        echo json_encode(['opciones' => []]);
        exit;
    }

    // 2. Obtener citas existentes de los terapeutas en la fecha
    $terapeuta_ids = array_map(fn($p) => $p['terapeuta_id'], $pasos);
    $citaModel = new Cita();
    $ocupadas = [];
    foreach ($terapeuta_ids as $tid) {
        $citas = $citaModel->getByTerapeutaYFecha($tid, $fecha);
        foreach ($citas as $c) {
            $ocupadas[$tid][] = [
                'inicio' => $c['hora_inicio'],
                'fin' => $c['hora_fin']
            ];
        }
    }

    // 3. Buscar bloques consecutivos disponibles
    $opciones = [];
    for ($i = 0; $i <= count($horarios) - count($pasos); $i++) {
        $bloque = array_slice($horarios, $i, count($pasos));
        $disponible = true;
        $duracion_total = 0;
        foreach ($pasos as $idx => $paso) {
            $tid = $paso['terapeuta_id'];
            $h_ini = $bloque[$idx]['hora_inicio'];
            $h_fin = $bloque[$idx]['hora_fin'];
            $duracion = $paso['duracion'] ?? 15; // Duración por defecto 15 minutos
            $duracion_total += $duracion;
            
            // Calcular la hora de fin real basada en la duración
            $h_ini_timestamp = strtotime($h_ini);
            $h_fin_real = date('H:i:s', $h_ini_timestamp + ($duracion * 60));
            
            // Verificar si el terapeuta está ocupado en ese bloque
            if (isset($ocupadas[$tid])) {
                foreach ($ocupadas[$tid] as $cita) {
                    if (!($h_fin_real <= $cita['inicio'] || $h_ini >= $cita['fin'])) {
                        $disponible = false;
                        break 2;
                    }
                }
            }
        }
        if ($disponible) {
            $opciones[] = [
                'inicio' => $bloque[0]['hora_inicio'],
                'fin' => $bloque[count($bloque)-1]['hora_fin'],
                'bloques' => $bloque,
                'duracion_total' => $duracion_total
            ];
        }
    }

    // Si la acción es sugerir, devolver opciones
    if ($accion === 'sugerir') {
        // Enriquecer con nombres
        $terapeutaModel = new Terapeuta();
        $especialidades = [];
        $terapeutas = $terapeutaModel->getAll();
        foreach ($pasos as $idx => $paso) {
            foreach ($terapeutas as $t) {
                if ($t['id'] == $paso['terapeuta_id']) {
                    $pasos[$idx]['terapeuta_nombre'] = $t['nombre'];
                    $pasos[$idx]['especialidad_nombre'] = $t['especialidad'];
                }
            }
        }
        $sugerencias = [];
        foreach ($opciones as $op) {
            $detalle = [];
            foreach ($pasos as $idx => $paso) {
                $h_ini = $op['bloques'][$idx]['hora_inicio'];
                $h_ini_timestamp = strtotime($h_ini);
                $h_fin_real = date('H:i:s', $h_ini_timestamp + ($paso['duracion'] * 60));
                $detalle[] = [
                    'nombre' => $paso['especialidad_nombre'],
                    'terapeuta' => $paso['terapeuta_nombre'],
                    'hora' => $h_ini . '-' . $h_fin_real,
                    'duracion' => $paso['duracion']
                ];
            }
            $sugerencias[] = [
                'inicio' => $op['inicio'],
                'fin' => $op['fin'],
                'pasos' => $detalle,
                'duracion_total' => $op['duracion_total']
            ];
        }
        echo json_encode(['opciones' => $sugerencias]);
        exit;
    }

    // Si la acción es agendar, crear las citas
    if ($accion === 'agendar' && isset($opcion) && isset($opciones[$opcion])) {
        foreach ($pasos as $idx => $paso) {
            $h_ini = $opciones[$opcion]['bloques'][$idx]['hora_inicio'];
            $h_ini_timestamp = strtotime($h_ini);
            $h_fin_real = date('H:i:s', $h_ini_timestamp + ($paso['duracion'] * 60));
            $citaModel->create(
                $paciente_id,
                $paso['terapeuta_id'],
                $fecha,
                $h_ini,
                $h_fin_real,
                null,
                $idx+1
            );
        }
        echo json_encode(['success' => true]);
        exit;
    }

    echo json_encode(['error' => 'No se pudo agendar']);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Método no permitido']); 