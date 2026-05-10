<?php

namespace Http;

/**
 * HTTP Response — status code & header giống hướng tiếp cận
 * {@see https://github.com/afgprogrammer/PHP-MVC-REST-API/tree/master/System/Http}
 *
 * @package Http
 */
class Response
{
    /** @var array<int, string> */
    protected $headers = [];

    /** @var array<int, string> */
    protected $statusTexts = [
        // INFORMATIONAL
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        // SUCCESS
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        // REDIRECTION
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        // SERVER ERROR
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];

    /** @var string */
    protected $version;

    /** @var mixed */
    protected $content;

    public function __construct()
    {
        $this->setVersion('1.1');
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getStatusCodeText(int $code): string
    {
        return isset($this->statusTexts[$code])
            ? $this->statusTexts[$code]
            : 'unknown status';
    }

    public function setHeader(string $header): void
    {
        $this->headers[] = $header;
    }

    /** @return array<int, string> */
    public function getHeader(): array
    {
        return $this->headers;
    }

    /**
     * @param mixed $content Du lieu se json_encode khi render
     */
    public function setContent($content): void
    {
        $this->content = json_encode($content);
    }

    /** @return string|null */
    public function getContent()
    {
        return $this->content;
    }

    public function redirect(string $url): void
    {
        if ($url === '') {
            trigger_error('Cannot redirect to an empty URL.');
            exit;
        }

        header(
            'Location: ' . str_replace(['&', "\n", "\r"], ['&', '', ''], $url),
            true,
            302
        );
        exit;
    }

    public function isInvalid(int $statusCode): bool
    {
        return $statusCode < 100 || $statusCode >= 600;
    }

    /**
     * Dat ma trang thai HTTP (tuong duoc sendStatus trong repo tham khao).
     */
    public function sendStatus($code): void
    {
        $code = (int) $code;
        if (!$this->isInvalid($code)) {
            http_response_code($code);
        }
    }

    public function render(): void
    {
        if ($this->content !== null && $this->content !== '') {
            $output = $this->content;

            if (!headers_sent()) {
                foreach ($this->headers as $header) {
                    header($header, true);
                }
            }

            echo $output;
        }
    }

    /**
     * Tra JSON cho REST API (Content-Type + CORS + thoat).
     *
     * @param array<string, mixed> $payload
     */
    public function json(array $payload, int $status = 200): void
    {
        $this->sendStatus($status);
        header('Content-Type: application/json; charset=utf-8');
        $this->sendCorsHeaders();

        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /** HTTP 204 — thanh cong, khong body */
    public function noContent(): void
    {
        $this->sendStatus(204);
        $this->sendCorsHeaders();
        exit;
    }

    private function sendCorsHeaders(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }
}
