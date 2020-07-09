<?php

namespace Anik\ElasticApm\Exceptions;

use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler implements ExceptionHandler
{
    private $primaryHandler;
    private $ignoredExceptions = [
        NotFoundHttpException::class,
    ];

    public function __construct (ExceptionHandler $primaryHandler, array $ignoredExceptions = []) {
        $this->primaryHandler = $primaryHandler;

        if ($ignoredExceptions) {
            $this->ignoredExceptions = $ignoredExceptions;
        }
    }

    private function logException (Exception $e) {
        $depth = config('elastic-apm.error.trace_depth', 30);
        $traces = collect($e->getTrace())->take($depth);

        return;
    }

    public function shouldReport ($e) {
        foreach ( $this->ignoredExceptions as $type ) {
            if ($e instanceof $type) {
                return false;
            }
        }

        return true;
    }

    public function report (Exception $e) {
        // primary handler => (mainly) App\Exceptions\Handler.php will handle Error logging on application log.
        $this->primaryHandler->report($e);

        if ($this->shouldReport($e)) {
            $this->logException($e);
        }
    }

    public function render ($request, Exception $e) {
        return $this->primaryHandler->render($request, $e);
    }

    public function renderForConsole ($output, Exception $e) {
        return $this->primaryHandler->renderForConsole($output, $e);
    }
}