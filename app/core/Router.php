<?php
declare(strict_types=1);

class Router
{
    private array $routes = [];

    public function get(string $path, $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, $handler): void
    {
        $this->routes[$method][] = [
            'path' => $path,
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = $this->stripBasePath($uri);
        $path = $path === '' ? '/' : $path;
        if ($path !== '/') {
            $path = '/' . trim($path, '/');
        }

        $routes = $this->routes[$method] ?? [];
        foreach ($routes as $route) {
            $params = [];
            if ($this->match($route['path'], $path, $params)) {
                $this->invoke($route['handler'], $params);
                return;
            }
        }

        http_response_code(404);
        echo '404 Not Found';
    }

    public function debug(string $method, string $uri): array
    {
        $path = $this->stripBasePath($uri);
        $path = $path === '' ? '/' : $path;
        if ($path !== '/') {
            $path = '/' . trim($path, '/');
        }

        $routes = $this->routes[$method] ?? [];
        $routePaths = array_map(static function (array $route): string {
            return $route['path'];
        }, $routes);

        $matched = null;
        foreach ($routes as $route) {
            $params = [];
            if ($this->match($route['path'], $path, $params)) {
                $matched = $route['path'];
                break;
            }
        }

        return [
            'path' => $path,
            'matched' => $matched,
            'routes' => $routePaths,
        ];
    }

    private function match(string $routePath, string $path, array &$params): bool
    {
        $params = [];
        if ($routePath === '/') {
            return $path === '/';
        }
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $routePath);
        $pattern = '#^' . rtrim($pattern, '/') . '$#';

        $normalizedPath = $path === '/' ? '/' : rtrim($path, '/');
        if ($routePath !== '/' && $routePath !== '') {
            $routePath = rtrim($routePath, '/');
        }

        if (preg_match($pattern, $normalizedPath, $matches)) {
            foreach ($matches as $key => $value) {
                if (!is_int($key)) {
                    $params[$key] = $value;
                }
            }
            return true;
        }

        return false;
    }

    private function invoke($handler, array $params): void
    {
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }

        if (is_string($handler) && strpos($handler, '@') !== false) {
            [$class, $method] = explode('@', $handler, 2);
            if (!class_exists($class)) {
                throw new RuntimeException('Controller not found: ' . $class);
            }
            $controller = new $class();
            if (!method_exists($controller, $method)) {
                throw new RuntimeException('Action not found: ' . $method);
            }
            if (method_exists($controller, 'setParams')) {
                $controller->setParams($params);
            }
            call_user_func([$controller, $method]);
            return;
        }

        throw new RuntimeException('Invalid route handler.');
    }

    private function stripBasePath(string $uri): string
    {
        $uri = '/' . ltrim($uri, '/');
        $base = rtrim((string) config('base_path', ''), '/');
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $scriptDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

        $candidates = array_filter(array_unique([
            $base,
            $scriptDir,
            rtrim($scriptDir . '/public', '/'),
        ]));

        foreach ($candidates as $candidate) {
            if ($candidate !== '' && strpos($uri, $candidate) === 0) {
                $uri = substr($uri, strlen($candidate));
                break;
            }
        }
        if (strpos($uri, '/index.php') === 0) {
            $uri = substr($uri, strlen('/index.php'));
        }
        return $uri;
    }
}
