<?php

namespace Anik\ElasticApm\Providers;

use Illuminate\Support\ServiceProvider;

class ElasticApmProvider extends ServiceProvider
{
    public function register () {
        $this->publishes([
            __DIR__ . '/../config/elastic-apm.php' => config_path('elastic-apm.php'),
        ]);
    }
}
