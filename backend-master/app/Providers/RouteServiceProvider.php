<?php

namespace App\Providers;

use App\Http\Router\Router;
use App\User;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

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
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot() {
        router()->model("user", User::class);

        parent::boot();
    }

    public function register() {
        parent::register();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map() {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapAuthRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes() {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes() {
        Route::prefix('api')
            ->middleware('ajax_api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    /**
     * Define the "auth" routes for the application.
     *
     * These routes for authentication.
     *
     * @return void
     */
    protected function mapAuthRoutes() {
        Route::prefix('auth')
            ->namespace($this->namespace)
            ->group(base_path('routes/auth.php'));
    }

    /**
     * Pass dynamic methods onto the router instance.
     *
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     */
    public function __call($method, $parameters) {
        return call_user_func_array(
            [$this->app->make(Router::class), $method], $parameters
        );
    }
}
