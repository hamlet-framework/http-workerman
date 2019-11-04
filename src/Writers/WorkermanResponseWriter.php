<?php

namespace Hamlet\Http\Swoole\Writers;

use Hamlet\Http\Writers\ResponseWriter;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http;

class WorkermanResponseWriter implements ResponseWriter
{
    /**
     * @var TcpConnection
     */
    private $connection;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var string
     */
    private $payload;

    public function __construct(TcpConnection $connection)
    {
        $this->connection = $connection;
        $this->statusCode = 200;
        $this->headers = [
            'Server' => 'Workerman',
            'Date'   => gmdate('D, d M Y H:i:s') . ' GMT'
        ];
        $this->payload = '';
    }

    /**
     * @param int $code
     * @param string|null $line
     * @return void
     */
    public function status(int $code, string $line = null)
    {
        $this->statusCode = $code;
    }

    /**
     * @param string $key
     * @param string $value
     * @return void
     */
    public function header(string $key, string $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * @param string $payload
     * @return void
     */
    public function writeAndEnd(string $payload)
    {
        $this->payload = $payload;
        $this->end();
    }

    /**
     * @return void
     */
    public function end()
    {
        Http::responseCode($this->statusCode);
        unset($this->headers['Content-Length']);
        foreach ($this->headers as $key => $value) {
            Http::header($key . ': ' . $value);
        }
        $this->connection->send($this->payload);
    }

    /**
     * @param string $name
     * @param string $value
     * @param int $expires
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httpOnly
     * @return void
     */
    public function cookie(string $name, string $value, int $expires, string $path, string $domain = '', bool $secure = false, bool $httpOnly = false)
    {
        Http::setcookie($name, $value, $expires, $path, $domain, $secure, $httpOnly);
    }
}
