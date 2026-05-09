<?php

class App {
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        // Xac dinh controller can goi
        $controllerName = 'HomeController';
        if (!empty($url[0])) {
            $controllerCandidate = ucwords($url[0]) . 'Controller';
            if (file_exists(APP . '/controllers/' . $controllerCandidate . '.php')) {
                $controllerName = $controllerCandidate;
                unset($url[0]);
            }
        }
        
        require_once APP . '/controllers/' . $controllerName . '.php';
        $this->controller = new $controllerName;

        // Xac dinh method (ho tro kebab-case va snake_case sang camelCase)
        $methodName = 'index';
        if (isset($url[1])) {
            $rawMethod = $url[1];
            $camelMethod = $this->toCamelCase($rawMethod);
            if (method_exists($this->controller, $camelMethod)) {
                $methodName = $camelMethod;
                unset($url[1]);
            } elseif (method_exists($this->controller, $rawMethod)) {
                // Du phong: neu co dung ten method goc thi cho phep goi
                $methodName = $rawMethod;
                unset($url[1]);
            }
        }
        $this->method = $methodName;


        // Lay tham so tren url
        $this->params = $url ? array_values($url) : [];

        // Goi method cua controller voi tham so, co du phong an toan
        if (!method_exists($this->controller, $this->method)) {
            // Neu loi thi quay ve HomeController@index de tranh fatal error
            require_once APP . '/controllers/HomeController.php';
            $this->controller = new HomeController();
            $this->method = 'index';
            $this->params = [];
        }
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
    }

    // Chuyen "forgot-password" hoac "reset_password" thanh camelCase
    private function toCamelCase($string) {
        $string = strtolower($string);
        $parts = preg_split('/[-_]+/', $string);
        if (!$parts) return $string;
        $camel = array_shift($parts);
        foreach ($parts as $p) {
            $camel .= ucfirst($p);
        }
        return $camel;
    }
}
