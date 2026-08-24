<?php
    require_once "login.php";
    require_once "gallery.php";
    require_once "setup.php";
    class Router 
    {
        private static $router;
        private array $routes;    // an associative array of routes where the route is the key and the value is the controller of the route

        public function __construct() {
            // echo "<p>Router object has been constructed</p><br>";
            $this->routes = [];
        }
        // this function takes the uri and a callable function for the respective URI's controller
        public function add_route(string $uri, callable $controller): void {
            // echo "adding $uri to the array of routes<br>";
            $this->routes[$uri] = $controller;
            
        }
        public static function get_router(): self {
            if (!isset(self::$router)) {
                self::$router = new self();
            }
            return self::$router;
        }

        public function print_routes(): void {
            echo "Routes<br>";
            foreach ($this->routes as $key => $value) {
                echo "$key: $value<br>";
            }
        }

        public function dispatch(string $uri): string {
            // clean the uri string
            foreach($this->routes as $route_uri => $view) {
                if ($uri == "/") {
                    return $this->routes["/gallery"]();
                }
                if ($uri == $route_uri) {
                    return $this->routes[$uri]();
                    // echo "<p>found route</p><br>";
                    // $this->dispatch($uri);
                }
            }
            return "404 not found";
        }
    }
    function sanitize_url(string $url): string {
        $sanitized = filter_var($url, FILTER_SANITIZE_URL);
        return $sanitized;
    }

    $request_uri = $_SERVER["REQUEST_URI"];
    $router = Router::get_router();

    $router->add_route("/login", "login");
    $router->add_route("/register", "register");
    $router->add_route("/gallery", "gallery");
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
        echo $router->dispatch($request_uri)
    ?>
</body>
</html>