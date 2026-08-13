<?php
    require_once "login.php";
    class Router 
    {
        private static $router;
        private array $routes;    // an associative array of routes where the route is the key and the value is the controller of the route

        public function __construct() {
            echo "Router object has been constructed<br>";
            $this->routes = [];
        }
        // this function takes the uri and a callable function for the respective URI's controller
        public function add_route(string $uri, callable $controller) {
            echo "adding $uri to the array of routes<br>";
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
    }
    $request = $_SERVER["REQUEST_URI"];
    // adds the route to the routes 
    $router = new Router();
    
    $router->add_route("/login", login_page());
    // $router->print_routes();
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
    <h1>Request: <?php echo $request ?></h1>
</body>
</html>