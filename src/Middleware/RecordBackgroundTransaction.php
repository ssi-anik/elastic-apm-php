<?php

namespace Anik\ElasticApm\Middleware;

use Anik\ElasticApm\Spans\BackgroundJobSpan;
use Anik\ElasticApm\Transaction;
use Carbon\Carbon;
use Closure;

class RecordBackgroundTransaction
{
    public function handle($job, Closure $next)
    {
        if (false === config('elastic-apm.active')) {
            return $next($job);
        }
        // set transaction for the job
        $transaction = new Transaction();
        $transaction->setName($this->getTransactionName($job))->setType($this->getTransactionType());
        app('apm-agent')->setTransaction($transaction);

        // Set span for the job it's processing
        $jobClass = get_class($job);
        app('apm-agent')->addSpan(new BackgroundJobSpan($jobClass, 'processing', Carbon::now()->toDateTimeString()));
        $response = $next($job);
        // set span for the class which is processed
        app('apm-agent')->addSpan(new BackgroundJobSpan($jobClass, 'processed', Carbon::now()->toDateTimeString()));
        app('apm-agent')->capture();

        return $response;
    }

    private function getTransactionName($job)
    {
        return get_class($job);
    }

    private function getTransactionType()
    {
        return config('elastic-apm.transaction.type.background', 'queue');
    }
}
