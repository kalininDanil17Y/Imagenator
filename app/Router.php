<?php
namespace App\Imagenator;

use App\Imagenator\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Router
 * @package App\Imagenator
 */
class Router extends View
{
    /**
     * @var array
     */
    private $routes = [];
    /**
     * @var array
     */
    private $routesPost = [];
    /**
     * @var string
     */
    protected $method;
    /**
     * @var Request
     */
    protected $request;

    /**
     * Router constructor.
     */
    public function __construct()
    {
        $this->request = Request::createFromGlobals();
        $this->method = $this->request->getMethod();
        $this->viewInit();
    }

    /**
     *
     */
    public function Handle()
    {

        if ($this->method === "GET") {
            $routes = $this->routes;
        } else {
            $routes = $this->routesPost;
        }

        if (empty($routes[$this->request->getPathInfo()])) {
            echo $this->buildTemplate('errors/404');
            die;
        }

        if (gettype($routes[$this->request->getPathInfo()]) === "array") {
            $class = 'Imagenator\Main\Controller\\' . $routes[$this->request->getPathInfo()][0];
            $controller = new $class();
            $response = $controller->{$routes[$this->request->getPathInfo()][1]}(new Response, $this->request);
        } else {
            $func = $routes[$this->request->getPathInfo()];
            $response = $func(new Response, $this->request);
        }
        if (!empty($response)) {
            $response = $response->end();
            echo $response['body'];
        }
    }

    /**
     * @param string $route
     * @param $controller
     */
    public function addRoute(string $route, $controller)
    {
        $this->routes[$route] = $controller;
    }

    /**
     * @param string $route
     * @param $controller
     */
    public function addRoutePost(string $route, $controller)
    {
        $this->routesPost[$route] = $controller;
    }
}