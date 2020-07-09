<?php

namespace Anik\ElasticApm\Facades;

use Anik\ElasticApm\Agent as ElasticAgent;
use Anik\ElasticApm\Transaction;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string instance()
 * @method static ElasticAgent setTransaction(Transaction $transaction)
 * @method static Transaction|null getTransaction(Transaction $transaction)
 * @method static void capture()
 *
 * @see \Anik\ElasticApm\Agent
 */
class Agent extends Facade
{
    protected static function getFacadeAccessor () {
        return 'elastic-apm-agent';
    }
}