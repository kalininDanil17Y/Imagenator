<?php
declare(strict_types=1);
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

        if (is_array($routes[$this->request->getPathInfo()])) {
            $class = 'Imagenator\Controller\\' . $routes[$this->request->getPathInfo()][0];
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
     * @param string $method
     * @param string $route
     * @param $controller
     */
    public function addRoute(string $method = "GET", string $route, $controller)
    {
        if ($method === "GET") {
            $this->routes[$route] = $controller;
        } else {
            $this->routesPost[$route] = $controller;
        }
    }
}