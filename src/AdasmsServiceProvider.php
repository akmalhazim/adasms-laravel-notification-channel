<?php

namespace NotificationChannels\Adasms;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\ServiceProvider;

/**
 * Class AdasmsServiceProvider.
 */
class AdasmsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->app->when(AdasmsChannel::class)
            ->needs(Adasms::class)
            ->give(static function () {
                return new Adasms(
                    config('services.adasms.token'),
                    app(HttpClient::class),
                    config('services.adasms.base_uri')
                );
            });
    }
}
