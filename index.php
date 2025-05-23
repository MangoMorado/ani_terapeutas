<?php
// Configuración de errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir archivo de configuración
require_once 'config/database.php';
require_once 'routes.php';

// Crear instancia del router
$router = new Router();

// Definir rutas
$router->addRoute('/pacientes', 'PacienteController', 'index');
$router->addRoute('/terapeutas', 'TerapeutaController', 'index');
$router->addRoute('/citas', 'CitaController', 'index');
$router->addRoute('/configuracion', 'ConfiguracionController', 'index');
$router->addRoute('/api/citas', 'CitaController', 'api');
$router->addRoute('/api/pacientes', 'PacienteController', 'api');
$router->addRoute('/api/terapeutas', 'TerapeutaController', 'api');

// Despachar la ruta
$router->dispatch();
?> 