<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Messaging;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind Firebase Messaging as a singleton using env-based credentials
        $this->app->singleton(Messaging::class, function () {
            $config = config('services.firebase');

            $serviceAccount = [
                'type' => 'service_account',
                'project_id' => $config['project_id'] ?? null,
                'client_email' => $config['client_email'] ?? null,
                'private_key' => $config['private_key'] ?? null,
            ];

            $factory = (new Factory())
                ->withServiceAccount($serviceAccount)
                ->withProjectId($config['project_id'] ?? null);

            return $factory->createMessaging();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        URL::forceScheme('https');
        // if (config('app.env') === 'local') {
        // }
    }
}
