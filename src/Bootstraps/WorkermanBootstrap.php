<?php

namespace Hamlet\Http\Workerman\Bootstraps;

use Hamlet\Http\Applications\AbstractApplication;
use Hamlet\Http\Requests\DefaultRequest;
use Hamlet\Http\Workerman\Writers\WorkermanResponseWriter;
use Workerman\Connection\TcpConnection;
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
        $worker->onMessage = static function (TcpConnection $connection) use ($application) {
            $request = new DefaultRequest();
            $writer = new WorkermanResponseWriter($connection);
            $response = $application->run($request);
            $application->output($request, $response, $writer);
        };
        Worker::runAll();
    }
}
