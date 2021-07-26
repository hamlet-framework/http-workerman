<?php

namespace Hamlet\Http\Workerman\Requests;

use Hamlet\Http\Message\ServerRequest;
use Hamlet\Http\Requests\Request;
use Hamlet\Http\Requests\RequestTrait;

class WorkermanRequest extends ServerRequest implements Request
{
    use RequestTrait;

    /** @var string|null */
    protected $path;

    public function __construct(\Workerman\Protocols\Http\Request $request)
    {
        parent::__construct();

        $this->method          = strtoupper($request->server['request_method'] ?? 'GET');
        $this->cookieParams    = $request->cookie();
        $this->queryParams     = $request->get() ?? [];
        $this->parsedBody      = $request->post() ?? [];
        $this->path            = $request->path();

        $this->protocolVersionGenerator = function () use ($request) {
            return $request->protocolVersion();
        };
        $this->bodyGenerator = function () use ($request) {
            return $request->rawBody();
        };
        $this->headersGenerator = function () use ($request) {
            return $request->header();
        };
        $this->uriGenerator = function () use ($request) {
            return $request->uri();
        };
        $this->uploadedFilesGenerator = function () use ($request) {
            return $request->file();
        };
    }

    public function getPath(): string
    {
        if ($this->path === null) {
            $this->path = $this->getUri()->getPath();
        }
        return $this->path;
    }
}
