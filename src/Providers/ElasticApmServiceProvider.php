<?php

namespace Anik\ElasticApm\Providers;

use Anik\ElasticApm\Agent;
use Anik\ElasticApm\Middleware\RecordBackgroundTransaction;
use Anik\ElasticApm\Middleware\RecordForegroundTransaction;
use Illuminate\Support\ServiceProvider;

class ElasticApmServiceProvider extends ServiceProvider
{
    public function boot () {
        if ($this->app->runningInConsole()) {
            $this->publishAssets();
        }
    }

    public function register () {
        $this->registerApmAgent();
        $this->registerMiddleware();
    }

    private function registerApmAgent () {
        $this->app->singleton(Agent::class, function ($app) {
            return Agent::instance();
        });
        $this->app->alias(Agent::class, 'elastic-apm-agent');
        $this->app->alias(Agent::class, 'elastic-apm');
        $this->app->alias(Agent::class, 'apm-agent');
    }

    private function registerMiddleware () {
        $this->app->singleton(RecordForegroundTransaction::class);
    }

    private function publishAssets () {
        $this->publishes([ __DIR__ . '/../config/elastic-apm.php' => config_path('elastic-apm.php'), ]);
    }

    public function provides () {
        return [
            Agent::class,
            RecordForegroundTransaction::class,
            'elastic-apm-agent',
            'elastic-apm',
            'apm-agent',
        ];
    }
}
