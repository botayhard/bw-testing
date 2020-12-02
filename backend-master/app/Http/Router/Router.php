<?php

namespace App\Http\Router;

use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Route as RouteRegistrar;

class Router extends \Illuminate\Routing\Router
{
    public function __construct(Dispatcher $events, Container $container = null) {
        parent::__construct($events, $container);
        $this->routes = new RouteCollection();
    }

    protected function newRoute($methods, $uri, $action) {
        return (new Route($methods, $uri, $action))
            ->setRouter($this)
            ->setContainer($this->container);
    }

    protected function findRoute($request) {
        /** @var Route $route */
        $this->current = $route = $this->routes->match($request);

        $this->container->instance(Route::class, $route);

        return $route;
    }

    public function attach($path) {
        if (is_dir(base_path() . '/routes' . $path)) {
            foreach (glob(base_path() . '/routes' . $path . '/*') as $k => $route_file) {
                $route_file = substr($route_file, strpos($route_file, "routes/") + strlen("routes"));

                $this->attach($route_file);
            };

            return null;
        }

        return RouteRegistrar::
        middleware('ajax_api')
            ->group(base_path('routes' . $path));
    }

    // For the glory of documentation!

    /**
     * @param string $uri
     * @param null $action
     * @return Route|\Illuminate\Routing\Route
     */
    public function get($uri, $action = null) {
        return parent::get($uri, $action);
    }

    /**
     * @param string $uri
     * @param null $action
     * @return Route|\Illuminate\Routing\Route
     */
    public function post($uri, $action = null) {
        return parent::post($uri, $action);
    }

    /**
     * @param string $uri
     * @param null $action
     * @return Route|\Illuminate\Routing\Route
     */
    public function put($uri, $action = null) {
        return parent::put($uri, $action);
    }

    /**
     * @param string $uri
     * @param null $action
     * @return Route|\Illuminate\Routing\Route
     */
    public function patch($uri, $action = null) {
        return parent::patch($uri, $action);
    }

    /**
     * @param string $uri
     * @param null $action
     * @return Route|\Illuminate\Routing\Route
     */
    public function delete($uri, $action = null) {
        return parent::delete($uri, $action);
    }

    /**
     * @param string $uri
     * @param null $action
     * @return Route|\Illuminate\Routing\Route
     */
    public function options($uri, $action = null) {
        return parent::options($uri, $action);
    }

    /**
     * @param string $uri
     * @param null $action
     * @return Route|\Illuminate\Routing\Route
     */
    public function any($uri, $action = null) {
        return parent::any($uri, $action);
    }
}