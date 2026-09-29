<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/core/Router.php';
require_once __DIR__ . '/../app/core/Controller.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Model.php';
require_once __DIR__ . '/../app/core/Auth.php';
require_once __DIR__ . '/../app/core/Validator.php';
require_once __DIR__ . '/../app/core/SampleData.php';

spl_autoload_register(function (string $class): void {
    $paths = [
        __DIR__ . '/../app/controllers/' . $class . '.php',
        __DIR__ . '/../app/models/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

$router = new Router();
require_once __DIR__ . '/../config/routes.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if (isset($_GET['debug']) && $_GET['debug'] === '1') {
    header('Content-Type: text/plain; charset=UTF-8');
    $debug = $router->debug($method, $uri);
    echo "REQUEST_URI: " . ($uri ?? '') . PHP_EOL;
    echo "METHOD: " . $method . PHP_EOL;
    echo "BASE_PATH: " . (string) config('base_path', '') . PHP_EOL;
    echo "MATCH_PATH: " . $debug['path'] . PHP_EOL;
    echo "MATCHED_ROUTE: " . ($debug['matched'] ?? 'none') . PHP_EOL;
    echo "ROUTES: " . implode(', ', $debug['routes']) . PHP_EOL;
    exit;
}

$router->dispatch($method, $uri);
