<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
    * This namespace is applied to your controller routes.
    *
    * In addition, it is set as the URL generator's root namespace.
    *
    * @var string
    */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        Route::prefix('v1')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/auth.php'));

        Route::prefix('v1')
             ->middleware('guest')
             ->namespace($this->namespace)
             ->group(base_path('routes/guest.php'));
    }
}
