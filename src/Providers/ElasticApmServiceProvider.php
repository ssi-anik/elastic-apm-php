<?php

namespace Anik\ElasticApm\Providers;

use Anik\ElasticApm\Agent;
use Anik\ElasticApm\Middleware\RecordForegroundTransaction;
use Anik\ElasticApm\Spans\QuerySpan;
use Anik\ElasticApm\Spans\RedisSpan;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Redis\Events\CommandExecuted;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class ElasticApmServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $source = realpath($raw = __DIR__ . '/../config/elastic-apm.php') ?: $raw;

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('elastic-apm.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('elastic-apm');
        }

        $this->mergeConfigFrom($source, 'elastic-apm');
        if (config('elastic-apm.active') && config('elastic-apm.send_queries')) {
            $this->listenExecutedQueries();
        }

        if (config('elastic-apm.active') && config('elastic-apm.send_redis')) {
            $this->listenExecutedRedis();
        }
    }

    public function register()
    {
        $this->registerApmAgent();
        $this->registerMiddleware();
    }

    private function listenExecutedQueries()
    {
        app('db')->listen(
            function (QueryExecuted $query) {
                $sql = $query->sql;
                $connection = $query->connection->getName();
                $duration = $query->time;

                app('apm-agent')->addSpan(new QuerySpan($connection, $sql, $duration));
            }
        );
    }

    private function listenExecutedRedis()
    {
        app('redis')->enableEvents();
        app('redis')->listen(
            function (CommandExecuted $command) {
                $cmd = $command->command;
                $connection = $command->connectionName;
                $duration = $command->time;

                app('apm-agent')->addSpan(new RedisSpan($connection, $cmd, $duration));
            }
        );
    }

    private function registerApmAgent()
    {
        $this->app->singleton(
            Agent::class,
            function ($app) {
                return Agent::instance();
            }
        );
        $this->app->alias(Agent::class, 'elastic-apm-agent');
        $this->app->alias(Agent::class, 'elastic-apm');
        $this->app->alias(Agent::class, 'apm-agent');
    }

    private function registerMiddleware()
    {
        $this->app->singleton(RecordForegroundTransaction::class);
    }

    public function provides()
    {
        return [
            Agent::class,
            RecordForegroundTransaction::class,
            'elastic-apm-agent',
            'elastic-apm',
            'apm-agent',
        ];
    }
}
