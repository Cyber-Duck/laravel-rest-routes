<?php namespace CyberDuck\RestRouter;

use Illuminate\Routing\RoutingServiceProvider;
use CyberDuck\RestRouter\Router;

class RestRouterServiceProvider extends RoutingServiceProvider {

    /**
     * Register the router instance.
     *
     * @return void
     */
    protected function registerRouter()
    {
        $this->app['router'] = $this->app->share(function($app)
        {
            $router = new Router($app['events'], $app);

            // If the current application environment is "testing", we will disable the
            // routing filters, since they can be tested independently of the routes
            // and just get in the way of our typical controller testing concerns.
            if ($app['env'] == 'testing')
            {
                $router->disableFilters();
            }

            return $router;
        });
    }
}
