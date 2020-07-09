<?php

namespace Anik\ElasticApm\Middleware;

use Closure;

class RecordBackgroundTransaction
{
    public function handle ($request, Closure $next) {
        return $next($request);
    }
}
