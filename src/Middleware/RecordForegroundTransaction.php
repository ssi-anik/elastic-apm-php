<?php

namespace Anik\ElasticApm\Middleware;

use Closure;

class RecordForegroundTransaction
{
    public function handle ($request, Closure $next) {
        return $next($request);
    }
}
