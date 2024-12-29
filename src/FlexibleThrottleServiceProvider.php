<?php

namespace Wnikk\FlexibleThrottle;

use Illuminate\Support\ServiceProvider;

class FlexibleThrottleServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/flexible-throttle.php', 'flexible-throttle');
        $this->app->singleton('flexibleipresolver', function ($app) {
            return new FlexibleIpResolver();
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/flexible-throttle.php' => config_path('flexible-throttle.php'),
        ]);

        $this->app['router']->aliasMiddleware('flexible.throttle', \Wnikk\FlexibleThrottle\Middleware\FlexibleThrottle::class);
    }
}