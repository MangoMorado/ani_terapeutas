<?php
class Environment {
    private static $isProduction = null;
    
    public static function isProduction() {
        if (self::$isProduction === null) {
            // Detectar si estamos en producción basado en el host
            $host = $_SERVER['HTTP_HOST'] ?? '';
            self::$isProduction = (
                strpos($host, 'localhost') === false && 
                strpos($host, '127.0.0.1') === false &&
                strpos($host, '::1') === false
            );
        }
        return self::$isProduction;
    }
    
    public static function getDatabaseConfig() {
        if (self::isProduction()) {
            // Configuración para producción
            return [
                'host' => 'localhost',
                'db_name' => 'ani_terapeutas', // Posible nombre diferente en producción
                'username' => 'root', // Cambiar según configuración del servidor
                'password' => '' // Cambiar según configuración del servidor
            ];
        } else {
            // Configuración para desarrollo local
            return [
                'host' => 'localhost',
                'db_name' => 'ani',
                'username' => 'root',
                'password' => ''
            ];
        }
    }
    
    public static function getBasePath() {
        if (self::isProduction()) {
            // En producción, la ruta base podría ser diferente
            return '/ani_terapeutas';
        } else {
            return '/ani_terapeutas';
        }
    }
}
?> 