<?php namespace CyberDuck\RestRouter;

use Illuminate\Routing\Router as LaravelRouter;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

/**
 * Class Router
 *
 * Extended router to add Rest routes matching our API design
 *
 * @todo Accept same options as $this->resource
 *
 * @category  Controllers
 * @package   cyber-duck_restrouter
 * @author    Cyber-Duck Ltd <info@cyber-duck.co.uk>
 * @copyright 2014 Cyber-Duck Ltd.
 * @license   Copyright http://www.cyber-duck.co.uk/
 * @link      http://www.cyber-duck.co.uk/
 */
class Router extends LaravelRouter
{
    /**
     * The default actions for a rest controller.
     *
     * @var array
     */
    protected $restDefaults = ['index', 'store', 'show', 'replace', 'update', 'destroy'];

    /**
     * Create a new Router instance.
     *
     * @param  \Illuminate\Events\Dispatcher $events
     * @param  \Illuminate\Container\Container $container
     *
     * @return void
     */
    public function __construct(Dispatcher $events, Container $container = null)
    {
        parent::__construct($events, $container);

        // Use _path to get the rest of a URL split into an array
        $this->pattern('_path', '.+');
        $this->bind('_path', function ($value) { return explode('/', $value); });
    }


    /**
     * Creates routes for a REST Resource. Similar to Laravel Resource routing,
     * but allowing for paths of unknown length to be matched and passed to the
     * controller.
     *
     * @param string $uri Base URI for this REST Resource
     * @param string $controller Controller to route to
     * @param array $options Includes only, except and model
     *
     * @return void
     */
    public function rest($uri, $controller, $options = [])
    {
        $this->group(['prefix' => $uri], function () use ($uri, $options, $controller) {
            // Switch / to . for use as route name
            $name = str_replace('/', '.', $uri);

            if (isset($options['model'])) {
                // Register the model so it's autoloaded and passed to the controller
                $this->model($options['model'], $options['model']);
            } else {
                // Grab an ID instead of a model instance
                $options['model'] = 'id';
            }

            $defaults = $this->restDefaults;

            // Reuse Resource methods, but with different defaults
            foreach ($this->getResourceMethods($defaults, $options) as $m) {
                $this->{'addRest'.ucfirst($m)}($name, $controller, $options);
            }

            // Register RESTful controller for any additional routes
            $this->controller('/', $controller);
        });
    }

    /**
     * Get the action array for a rest route.
     *
     * @param string $name
     * @param string $controller
     * @param string $method
     * @return array
     */
    protected function getRestAction($name, $controller, $method)
    {
        return ['as' => $name . '.' . $method, 'uses' => $controller . '@' . $method];
    }

    /**
     * Get the URI for a non-root rest route.
     *
     * @param string $model
     * @return string
     */
    protected function getRestUri($model)
    {
        return '/{' . $model . '}/{_path?}';
    }

    /**
     * Add the index method for a rest route.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array   $options
     * @return \Illuminate\Routing\Route
     */
    protected function addRestIndex($name, $controller, $options) {
        $action = $this->getRestAction($name, $controller, 'index');
        $this->get('/', $action);
    }

    /**
     * Add the store method for a rest route.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array   $options
     * @return \Illuminate\Routing\Route
     */
    protected function addRestStore($name, $controller, $options) {
        $action = $this->getRestAction($name, $controller, 'store');
        $this->post('/', $action);
    }

    /**
     * Add the show method for a rest route.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array   $options
     * @return \Illuminate\Routing\Route
     */
    protected function addRestShow($name, $controller, $options) {
        $action = $this->getRestAction($name, $controller, 'show');
        $uri = $this->getRestUri($options['model']);
        $this->get($uri, $action);
    }

    /**
     * Add the replace method for a rest route.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array   $options
     * @return \Illuminate\Routing\Route
     */
    protected function addRestReplace($name, $controller, $options) {
        $action = $this->getRestAction($name, $controller, 'replace');
        $uri = $this->getRestUri($options['model']);
        $this->put($uri, $action);
    }

    /**
     * Add the update method for a rest route.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array   $options
     * @return \Illuminate\Routing\Route
     */
    protected function addRestUpdate($name, $controller, $options) {
        $action = $this->getRestAction($name, $controller, 'update');
        $uri = $this->getRestUri($options['model']);
        $this->patch($uri, $action);
    }

    /**
     * Add the destroy method for a rest route.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array   $options
     * @return \Illuminate\Routing\Route
     */
    protected function addRestDestroy($name, $controller, $options) {
        $action = $this->getRestAction($name, $controller, 'destroy');
        $uri = $this->getRestUri($options['model']);
        $this->delete($uri, $action);
    }
}
