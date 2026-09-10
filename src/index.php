<?php
    if (session_status() === PHP_SESSION_NONE)
        session_start();
    require_once "login.php";
    require_once "gallery.php";
    require_once "setup.php";
    require_once "register.php";

    $pdo = setup_pdo();
    class Router 
    {
        private static $router;
        public array $routes = [];    // an associative array of routes where the route is the key and the value is the controller of the route


        # route: [
        #   "method": string => "GET",
        #   "route": string => "/gallery",
        #   "handler": callable => function(),
        #   "middleware": array => []
        # ]
        public function __construct() {
            $this->routes = [];
        }
        // register a route's GET request
        public function get(string $uri, callable $controller, array $middleware = []): void {
            // $this->routes["GET"][$uri] = $controller;
            $this->register_route('GET', $uri, $controller, $middleware);
        }
        // // register a route's POST request
        public function post(string $uri, callable $controller, array $middleware = []): void {
            // $this->routes["POST"][$uri] = $controller;
            $this->register_route('POST', $uri, $controller, $middleware);
        }

        private function register_route(string $method, string $uri, callable $handler, array $middleware): void {
            [$regex, $paramNames] = $this->compilePattern($uri);

            $this->routes[] = compact('method', 'regex', 'handler', 'middleware') + ['params' => $paramNames];
        }

        public static function get_router(): self {
            if (!isset(self::$router)) {
                self::$router = new self();
            }
            return self::$router;
        }

        public function dispatch(string $uri, $args = null): string {
            $method = $_SERVER["REQUEST_METHOD"];
            $path = $this->normalize_url($uri);
            $allowedMethods = [];
            foreach ($this->routes as $route) {
                if (!preg_match($route['regex'], $path, $matches)) continue;
                if ($route['method'] !== $method) {
                    $allowedMethods[] = $route['method'];
                    continue;
                }
                $params = array_combine($route['params'], array_slice($matches, 1));
                foreach($route['middleware'] as $mw) {
                    $result = $mw($params, $_POST);
                    if ($result !== null) return $result;
                }
                return call_user_func($route['handler'], $params, $_POST);
                // return call_user_func($route['handler'], $params, $_POST);
            }
            if ($allowedMethods) {
                http_response_code(405);
                header('Allow: ' . implode(', ', array_unique($allowedMethods)));
                return "405 method not allowed";
            }
            http_response_code(404);
            return "404 not found";
        }

        public function normalize_url (string $url) {
            $new_url = parse_url($url, PHP_URL_PATH);
            $new_url = rtrim($new_url, '/');
            return $new_url;
        }

        public function compilePattern(string $uri): array {
            $paramNames = [];
            $regexBody = preg_replace_callback(
                '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
                function ($m) use (&$paramNames) {
                    print_r($m);
                    $paramNames[] = $m[1];
                    return '([^/]+)';
                },
                $uri
            );
            return ['#^' . $regexBody . '$#', $paramNames];
        }
    }

    function test_email(array $params, array $body): string | null {
        if (!preg_match("/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/", $body["email"]))
            return "400 Bad content structure";
        return null;
    }

    // function test_username(array $params, array $body) {
        
    // }

    $router = Router::get_router();
    $method = $_SERVER['REQUEST_METHOD'];
    $router->get('/login', fn() => login_page());
    $router->post("/login", fn($params, $body) => login($pdo, $body));
    $router->get('/gallery', fn() => gallery());
    $router->get("/register", fn() => register());
    $router->post("/register", fn($params, $body) => register_user($pdo, new UserDetails(
        $body['username'], $body['firstname'], $body['password'], $body['email']
    )), [test_email(...)]);
    $router->get("/users", fn() => get_users($pdo));
    $router->post("/logout", fn() => logout());
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
        // $request_method = $_SERVER['REQUEST_METHOD'];
        // $user = null;
        // if ($request_method == 'POST' && $uri == '/register') {
        //     $user = new UserDetails($_POST['username'], $_POST['firstname'], $_POST['password'], $_POST['email']);
        // }
        // echo $router->dispatch($uri, $user);
        // print_r($router->routes);
        echo "<br>";
        echo $router->dispatch($_SERVER['REQUEST_URI']);
    ?>
</body>
</html>