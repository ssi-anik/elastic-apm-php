<?php

namespace Anik\ElasticApm\Exceptions;

use Anik\ElasticApm\Spans\ErrorSpan;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class HandlerThrowable implements ExceptionHandler
{
    private $primaryHandler;
    private $ignoredExceptions = [
        NotFoundHttpException::class,
    ];

    public function __construct(ExceptionHandler $primaryHandler, array $ignoredExceptions = [])
    {
        $this->primaryHandler = $primaryHandler;

        if ($ignoredExceptions) {
            $this->ignoredExceptions = $ignoredExceptions;
        }
    }

    private function logException(Throwable $e)
    {
        $depth = config('elastic-apm.error.trace_depth', 30);
        $traces = collect($e->getTrace())->take($depth)->map(
            function ($trace) {
                return [
                    'file/func' => $trace['file'] ?? ($trace['function'] ?? 'N/A'),
                    'line' => $trace['line'] ?? 'N/A',
                ];
            }
        );
        app('apm-agent')->addSpan(new ErrorSpan($e, $traces));

        return;
    }

    public function shouldReport($e)
    {
        foreach ($this->ignoredExceptions as $type) {
            if ($e instanceof $type) {
                return false;
            }
        }

        return true;
    }

    public function report(Throwable $e)
    {
        // primary handler => (mainly) App\Exceptions\Handler.php will handle Error logging on application log.
        $this->primaryHandler->report($e);

        if ($this->shouldReport($e)) {
            $this->logException($e);
        }
    }

    public function render($request, Throwable $e)
    {
        return $this->primaryHandler->render($request, $e);
    }

    public function renderForConsole($output, Throwable $e)
    {
        return $this->primaryHandler->renderForConsole($output, $e);
    }
}
