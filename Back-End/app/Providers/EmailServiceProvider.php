<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Library\Services\EmailService;
class EmailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Library\Services\EmailService', function ($app) {
            return new EmailService();
          });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
