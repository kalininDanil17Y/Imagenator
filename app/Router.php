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
    public function start()
    {
        if ($this->method === "GET") {
            $routes = $this->routes;
        } else {
            $routes = $this->routesPost;
        }

        $thisPath = $this->request->getPathInfo();

        if (!array_key_exists($thisPath, $routes)) {
            echo $this->buildTemplate('errors/404');
            die;
        }

        $controller = $routes[$thisPath];


        if (is_array($controller)) {
            [$className, $funcName] = $controller;
            $classPath = 'Imagenator\Controller\\' . $className;
            $class = new $classPath;
            $response = $class->{$funcName}(new Response, $this->request);
        } else if (is_string($controller)) {
            $response = $controller();
        } else {
            $response = $controller(new Response, $this->request);
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