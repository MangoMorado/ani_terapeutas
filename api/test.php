<?php
require_once __DIR__ . '/../config/environment.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

try {
    // Información del entorno
    $environment = [
        'is_production' => Environment::isProduction(),
        'host' => $_SERVER['HTTP_HOST'] ?? 'unknown',
        'server_name' => $_SERVER['SERVER_NAME'] ?? 'unknown',
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'unknown',
        'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'unknown'
    ];
    
    // Probar conexión a la base de datos
    $database = new Database();
    $connection = $database->getConnection();
    
    if ($connection) {
        $dbStatus = 'connected';
        $dbInfo = [
            'host' => $database->host ?? 'unknown',
            'database' => $database->db_name ?? 'unknown'
        ];
    } else {
        $dbStatus = 'failed';
        $dbInfo = null;
    }
    
    echo json_encode([
        'status' => 'success',
        'environment' => $environment,
        'database' => [
            'status' => $dbStatus,
            'info' => $dbInfo
        ],
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'environment' => [
            'is_production' => Environment::isProduction(),
            'host' => $_SERVER['HTTP_HOST'] ?? 'unknown'
        ]
    ]);
}
?> 