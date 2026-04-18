<?php

class Router
{
    private $routes = [];

    public function get($path, $handler)
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post($path, $handler)
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put($path, $handler)
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function patch($path, $handler)
    {
        $this->addRoute('PATCH', $path, $handler);
    }

    public function delete($path, $handler)
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    public function options($path, $handler)
    {
        $this->addRoute('OPTIONS', $path, $handler);
    }

    private function addRoute($method, $path, $handler)
    {
        $path = $this->normalizePath($path);

        $regex = preg_replace('#:([a-zA-Z_][a-zA-Z0-9_]*)#', '(?P<$1>[^/]+)', $path);
        $regex = '#^' . $regex . '$#';

        $this->routes[$method][] = [
            'path' => $path,
            'regex' => $regex,
            'handler' => $handler,
        ];
    }

    public function dispatch($requestPath, $requestMethod)
    {
        $method = strtoupper($requestMethod);
        $path = $this->normalizePath($requestPath);

        if (!isset($this->routes[$method])) {
            return false;
        }

        foreach ($this->routes[$method] as $route) {
            if (!preg_match($route['regex'], $path, $matches)) {
                continue;
            }

            $params = [];
            $namedParams = [];
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[] = $value;
                    $namedParams[$key] = $value;
                }
            }

            $this->executeHandler($route['handler'], $params, $namedParams);
            return true;
        }

        return false;
    }

    private function normalizePath($path)
    {
        $path = str_replace(' ', '-', (string)$path);
        $path = preg_replace('#/+#', '/', $path);
        $path = '/' . trim($path, '/');
        if ($path === '//') {
            $path = '/';
        }
        return $path;
    }

    private function executeHandler($handler, $params = [], $namedParams = [])
    {
        if (is_callable($handler)) {
            call_user_func($handler, $namedParams);
            return;
        }

        if (!is_string($handler) || strpos($handler, '@') === false) {
            throw new Exception('Route handler khong hop le');
        }

        list($controllerName, $methodName) = explode('@', $handler, 2);
        if (stripos($controllerName, 'Controller') === false) {
            $controllerName = ucfirst(strtolower($controllerName)) . 'Controller';
        }
        if (!class_exists($controllerName)) {
            throw new Exception('Controller khong ton tai: ' . $controllerName);
        }

        $controller = new $controllerName();
        if (!method_exists($controller, $methodName)) {
            throw new Exception('Method khong ton tai: ' . $methodName);
        }

        call_user_func_array([$controller, $methodName], $params);
    }
}
