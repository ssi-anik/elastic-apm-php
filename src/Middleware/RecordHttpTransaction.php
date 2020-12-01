<?php

namespace Anik\ElasticApm\Middleware;

use Anik\ElasticApm\Spans\HttpRequestSpan;
use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use function GuzzleHttp\Promise\rejection_for;

class RecordHttpTransaction
{
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $now = microtime(true);

            return $handler($request, $options)->then(
                $this->handleSuccess($request, $options, $now),
                $this->handleFailure($request, $options, $now)
            );
        };
    }

    private function duration($then)
    {
        return (microtime(true) - $then) * 1000;
    }

    private function handleSuccess(RequestInterface $request, array $options, $then): callable
    {
        return function (ResponseInterface $response) use ($request, $options, $then) {
            $method = $request->getMethod();
            $host = $request->getUri()->getHost();
            $path = $request->getUri()->getPath();
            $statusCode = $response->getStatusCode();
            $duration = $this->duration($then);

            app('apm-agent')->addSpan(
                new HttpRequestSpan(
                    $host,
                    $path,
                    $duration,
                    [
                        'method' => $method,
                        'status_code' => $statusCode,
                        'positive' => $statusCode >= 200 && $statusCode < 400,
                        'exception' => null,
                    ]
                )
            );

            return $response;
        };
    }

    private function handleFailure(RequestInterface $request, array $options, $then): callable
    {
        return function (Exception $reason) use ($request, $options, $then) {
            $method = $request->getMethod();
            $host = $request->getUri()->getHost();
            $path = $request->getUri()->getPath();
            $duration = $this->duration($then);

            app('apm-agent')->addSpan(
                new HttpRequestSpan(
                    $host,
                    $path,
                    $duration,
                    [
                        'method' => $method,
                        'status_code' => null,
                        'positive' => false,
                        'exception' => $reason->getMessage(),
                    ]
                )
            );

            return rejection_for($reason);
        };
    }
}
