<?php

namespace App\Providers;

use App\Helper\State;
use App\Helper\Country;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Country', function(){return new Country();});
        $this->app->bind('State', function(){return new State();});
    }
}
