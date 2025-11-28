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

            // Prefer a full JSON blob if provided
            $credentialsJson = $config['credentials_json'] ?? null;
            $serviceAccount = null;

            $normalize = function ($v) { return is_string($v) && trim($v) === '' ? null : $v; };

            if (!empty($credentialsJson)) {
                $decoded = json_decode($credentialsJson, true);
                if (is_array($decoded)) {
                    // Normalize empty strings to null for optional fields
                    foreach (['project_id','private_key_id','private_key','client_email','client_id','auth_uri','token_uri','auth_provider_x509_cert_url','client_x509_cert_url','type'] as $key) {
                        if (array_key_exists($key, $decoded)) {
                            $decoded[$key] = $normalize($decoded[$key]);
                        }
                    }
                    // Normalize escaped newlines in private key, if any
                    if (isset($decoded['private_key'])) {
                        $decoded['private_key'] = str_replace('\\n', "\n", $decoded['private_key']);
                    }
                    // Ensure type is set
                    $decoded['type'] = $decoded['type'] ?? 'service_account';
                    $serviceAccount = $decoded;
                }
            }

            if ($serviceAccount === null) {
                $serviceAccount = [
                    'type' => 'service_account',
                    'project_id' => $normalize($config['project_id'] ?? null),
                    'client_email' => $normalize($config['client_email'] ?? null),
                    'private_key' => isset($config['private_key']) ? $config['private_key'] : null,
                ];
            }

            $factory = (new Factory())
                ->withServiceAccount($serviceAccount);

            $projectId = $serviceAccount['project_id'] ?? ($config['project_id'] ?? null);
            if (!empty($projectId)) {
                $factory = $factory->withProjectId($projectId);
            }

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
