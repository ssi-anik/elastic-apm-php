<?php

namespace Anik\ElasticApm\Middleware;

use Anik\ElasticApm\Transaction;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

class RecordForegroundTransaction
{
    public function handle ($request, Closure $next) {
        $response = $next($request);
        $transaction = new Transaction();
        $transaction->setName($this->getTransactionName($request))->setType($this->getTransactionType());

        app('apm-agent')->setTransaction($transaction);

        return $response;
    }

    private function getTransactionType () {
        return config('elastic-apm.transaction.type.foreground', 'request');
    }

    private function getTransactionName (Request $request) {
        $route = $request->route();
        // Lumen returns ARRAY.
        if (is_array($route)) {
            // Try the assigned controller action
            if (isset($route[1]) && isset($route[1]['uses'])) {
                return $route[1]['uses'];
            } elseif (isset($route[1]) && isset($route[1]['as'])) {
                // Try named routes
                return $route[1]['as'];
            }
        } elseif ($route instanceof Route) {
            // Laravel returns Route also with other types
            if (is_string($uses = $route->getAction('uses'))) {
                return $uses;
            } elseif (is_string($as = $route->getAction('as'))) {
                return $as;
            }
        }

        // Either missed from lumen array or Missed from Laravel Route object
        if (!is_null($route)) {
            return sprintf('%s %s', $request->getMethod(), $request->path());
        }

        // Possibly 404
        return 'index.php';
    }

    public function terminate ($request, $response) {
        app('apm-agent')->capture();
        /*
        app('log')->info($response->getStatusCode());
        try {
            $this->agent->send();
        } catch ( \Throwable $t ) {
            Log::error($t);
        }*/
    }
}
