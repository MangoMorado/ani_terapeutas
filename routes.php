<?php
class Router {
    private $routes = [];
    private $currentRoute = '';

    public function __construct() {
        $this->currentRoute = $_SERVER['REQUEST_URI'];
        $this->currentRoute = str_replace('/ani_terapeutas', '', $this->currentRoute);
        $this->currentRoute = parse_url($this->currentRoute, PHP_URL_PATH);
    }

    public function addRoute($path, $controller, $action) {
        $this->routes[$path] = [
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function dispatch() {
        // Si la ruta está vacía o es '/', mostrar la página principal
        if ($this->currentRoute === '' || $this->currentRoute === '/') {
            require_once 'views/index.html';
            return;
        }

        // Verificar si la ruta existe
        if (isset($this->routes[$this->currentRoute])) {
            $route = $this->routes[$this->currentRoute];
            $controller = $route['controller'];
            $action = $route['action'];

            // Cargar el controlador
            require_once "controllers/{$controller}.php";
            $controllerInstance = new $controller();
            $controllerInstance->$action();
        } else {
            // Ruta no encontrada
            header("HTTP/1.0 404 Not Found");
            require_once 'views/404.html';
        }
    }
}
?> 