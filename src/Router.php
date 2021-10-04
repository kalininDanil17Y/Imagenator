<?php
namespace Imagenator\Main;

use Imagenator\Main\Response;
use Symfony\Component\HttpFoundation\Request;

class Router extends View
{
    private $routes = [];
    private $routesPost = [];

    protected $method;
    protected $request;

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
        $this->method = $this->request->getMethod();
        $this->viewInit();
    }

    public function Handle()
    {

        if ($this->method === "GET") {
            $routes = $this->routes;
        } else {
            $routes = $this->routesPost;
        }

        /*
         * Если страницы нет, выводим 404
         */
        if (empty($routes[$this->request->getPathInfo()])) {
            echo $this->buildTemplate('errors/404');
            die;
        }

        /*
         * Если контроллер является функцией, то выполняем её
         * иначе выполняем класс и метод который указан в массиве
         */
        if (gettype($routes[$this->request->getPathInfo()]) === "array") {
            $class = 'Imagenator\Main\Controllers\\' . $routes[$this->request->getPathInfo()][0];
            $controller = new $class();
            $response = $controller->{$routes[$this->request->getPathInfo()][1]}(new Response, $this->request);
        } else {
            $func = $routes[$this->request->getPathInfo()];
            $response = $func(new Response, $this->request);
        }
        $response = $response->end();
        echo $response['body'];
    }

    public function addRoute(string $route, $controller)
    {
        //Устанавливаем контроллер GET
        $this->routes[$route] = $controller;
    }

    public function addRoutePost(string $route, $controller)
    {
        //Устанавливаем контроллер POST
        $this->routesPost[$route] = $controller;
    }
}