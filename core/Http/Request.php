<?php

namespace Http;

class Request
{
    /** @var array<string, mixed> */
    public $request;

    /** @var array<string, mixed> */
    public $cookie;

    /** @var array<string, mixed> */
    public $files;

    public function __construct()
    {
        $this->request = $_REQUEST;
        $this->cookie = $this->clean($_COOKIE);
        $this->files = $this->clean($_FILES);
    }

    /**
     * @return mixed|null|array<string, mixed>
     */
    public function get(string $key = '')
    {
        if ($key !== '') {
            return isset($_GET[$key]) ? $this->clean($_GET[$key]) : null;
        }

        return $this->clean($_GET);
    }

    /**
     * @return mixed|null|array<string, mixed>
     */
    public function post(string $key = '')
    {
        if ($key !== '') {
            return isset($_POST[$key]) ? $this->clean($_POST[$key]) : null;
        }

        return $this->clean($_POST);
    }

    /**
     * Body JSON (php://input).
     *
     * @return mixed|null|array<string, mixed>
     */
    public function input(string $key = '')
    {
        $postdata = file_get_contents('php://input');
        if ($postdata === false || $postdata === '') {
            $request = [];
        } else {
            $decoded = json_decode($postdata, true);
            $request = is_array($decoded) ? $decoded : [];
        }

        if ($key !== '') {
            return isset($request[$key]) ? $this->clean($request[$key]) : null;
        }

        return $this->clean($request);
    }

    /**
     * @return mixed|null|array<string, mixed>
     */
    public function server(string $key = '')
    {
        if ($key !== '') {
            $k = strtoupper($key);
            return isset($_SERVER[$k]) ? $this->clean($_SERVER[$k]) : null;
        }

        return $this->clean($_SERVER);
    }

    public function getMethod(): string
    {
        $m = $this->server('REQUEST_METHOD');

        return strtoupper((string) ($m ?? 'GET'));
    }

    public function getClientIp(): string
    {
        $ip = $this->server('REMOTE_ADDR');

        return $ip !== null ? (string) $ip : '';
    }

    public function getUrl(): string
    {
        $uri = $this->server('REQUEST_URI');

        return $uri !== null ? (string) $uri : '';
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    private function clean($data)
    {
        if (is_array($data)) {
            $out = [];
            foreach ($data as $key => $value) {
                $out[$this->clean($key)] = $this->clean($value);
            }

            return $out;
        }

        return htmlspecialchars((string) $data, ENT_COMPAT, 'UTF-8');
    }
}
