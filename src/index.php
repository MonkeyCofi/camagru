<?php
    require_once "login.php";
    require_once "gallery.php";
    require_once "setup.php";
    require_once "register.php";

    $pdo = setup_pdo();
    class Router 
    {
        private static $router;
        private array $routes;    // an associative array of routes where the route is the key and the value is the controller of the route

        public function __construct() {
            $this->routes = [];
        }
        // register a route's GET request
        public function get(string $uri, callable $controller): void {
            $this->routes["GET"][$uri] = $controller;
        }
        // register a route's POST request
        public function post(string $uri, callable $controller): void {
            $this->routes["POST"][$uri] = $controller;
        }

        public static function get_router(): self {
            if (!isset(self::$router)) {
                self::$router = new self();
            }
            return self::$router;
        }

        public function dispatch(string $uri): string {
            $method = $_SERVER["REQUEST_METHOD"];
            if ($uri == '/') {
                return $this->routes['GET']['/gallery']();
            }
            return $this->routes[$method][$uri]() ?? "404 not found";
        }
    }

    $router = Router::get_router();
    $uri = $_SERVER['REQUEST_URI'];

    // register routes
    $router->get("/login", "login");    
    $router->get("/login", "login");
    $router->get("/gallery", "gallery");
    $router->get("/register", "register");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Camagru</title>
</head>
<body>
    <?php 
        include "navbar.php";
        echo $router->dispatch($uri)
        // echo $router->dispatch($request_uri)
    ?>
</body>
</html>