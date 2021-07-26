<?php

namespace Hamlet\Http\Workerman\Bootstraps;

use Hamlet\Http\Applications\AbstractApplication;
use Hamlet\Http\Workerman\Requests\WorkermanRequest;
use Hamlet\Http\Workerman\Writers\WorkermanResponseWriter;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;
use Workerman\Worker;

final class WorkermanBootstrap
{
    private function __construct()
    {
    }

    /**
     * @param string $host
     * @param int $port
     * @param AbstractApplication $application
     * @return void
     * @psalm-suppress ForbiddenCode
     * @psalm-suppress InvalidOperand
     * @psalm-suppress MissingClosureReturnType
     * @psalm-suppress PossiblyInvalidPropertyAssignmentValue
     * @psalm-suppress PossiblyNullOperand
     */
    public static function run(string $host, int $port, AbstractApplication $application)
    {
        $worker = new Worker('http://' . $host . ':' . $port);
        $worker->count = shell_exec('nproc') * 3;
        $worker->onMessage = static function (TcpConnection $connection, Request $request) use ($application) {
            $request = new WorkermanRequest($request);
            $writer = new WorkermanResponseWriter($connection);
            $response = $application->run($request);
            $application->output($request, $response, $writer);
        };
        Worker::runAll();
    }
}
