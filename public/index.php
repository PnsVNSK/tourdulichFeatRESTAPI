<?php
session_start();

define("ROOT", dirname(__DIR__));
define("APP", ROOT . "/app");

// Tao base url co ho tro thu muc con
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];

// Tinh duong dan goc cua du an (len 1 cap tu thu muc public)
// Vi du SCRIPT_NAME la /tour1/public/index.php thi lay /tour1
$scriptPath = $_SERVER['SCRIPT_NAME'];
$basePath = dirname(dirname($scriptPath));

// Chuan hoa duong dan
$basePath = rtrim(str_replace('\\', '/', $basePath), '/');

// Dam bao duong dan bat dau bang /
if ($basePath === '' || $basePath === '.') {
    $basePath = '';
} elseif ($basePath[0] !== '/') {
    $basePath = '/' . $basePath;
}

define("BASE_URL", $protocol . '://' . $host . $basePath . '/');

// Nap tu dong lop ho tro
require_once ROOT . "/core/Helper.php";

spl_autoload_register(function ($className) {
    $corePath = ROOT . "/core/" . str_replace("\\", "/", $className) . ".php";
    if (file_exists($corePath)) {
        require_once $corePath;
        return;
    }

    $controllerPath =
        APP . "/controllers/" . str_replace("\\", "/", $className) . ".php";
    if (file_exists($controllerPath)) {
        require_once $controllerPath;
        return;
    }

    $modelPath = APP . "/models/" . str_replace("\\", "/", $className) . ".php";
    if (file_exists($modelPath)) {
        require_once $modelPath;
    }
});

require_once ROOT . "/core/App.php";
require_once ROOT . "/Router/Router.php";

$router = new Router();
$routeFile = ROOT . "/Router/Route.php";
if (file_exists($routeFile)) {
    require_once $routeFile;
}

$requestPath = isset($_GET['url']) ? trim($_GET['url'], '/') : '';
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Dieu huong api theo route khai bao; neu khong khop thi dung app mvc cu
$isMatched = $router->dispatch($requestPath, $requestMethod);
if (!$isMatched) {
    $app = new App();
}

