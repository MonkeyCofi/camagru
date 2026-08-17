<?php
    require_once "login.php";
    require_once "gallery.php";
    // require_once "navbar.php";
    class Router 
    {
        private static $router;
        private array $routes;    // an associative array of routes where the route is the key and the value is the controller of the route

        public function __construct() {
            // echo "<p>Router object has been constructed</p><br>";
            $this->routes = [];
        }
        // this function takes the uri and a callable function for the respective URI's controller
        public function add_route(string $uri, callable $controller) {
            // echo "adding $uri to the array of routes<br>";
            $this->routes[$uri] = $controller;
            
        }
        public static function get_router(): self {
            if (!isset(self::$router)) {
                self::$router = new self();
            }
            return self::$router;
        }

        public function print_routes() {
            echo "Routes<br>";
            foreach ($this->routes as $key => $value) {
                echo "$key: $value<br>";
            }
        }

        public function dispatch(string $uri) {
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
        }
    }
    $request_uri = $_SERVER["REQUEST_URI"];
    $sanitized_uri = filter_var($request_uri, FILTER_SANITIZE_URL);
    if (filter_var($sanitized_uri, FILTER_VALIDATE_URL)) {
        echo "<p>URI is valid</p>";
    } else {
        echo "<p>URI is invalid</p>";
    }
    echo "<p style='font-size: 32px;'>Route: $request_uri</p>";
    // adds the route to the routes 
    $router = Router::get_router();
    $router->add_route("/login", "login");
    $router->add_route("/register", "register");
    $router->add_route("/gallery", "gallery");
    
    // each time a user queries for a route,
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
    <!-- <h1>Camagru</h1> -->
    <?php 
        include "navbar.php";
        echo $router->dispatch($request_uri)
    ?>
</body>
</html>