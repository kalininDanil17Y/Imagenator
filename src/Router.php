<?php
namespace Imagenator\Main;

use Imagenator\Main\Response;
use Symfony\Component\HttpFoundation\Request;

class Router extends View
{
    private $routes = [];
    private $routesPost = [];

    private $response;
    protected $method;
    protected $request;

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
        $this->method = $this->request->getMethod();
        $this->init();
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
            $this->printTemplate('errors/404');
            die;
        }

        /*
         * Если контроллер является функцией, то выполняем её
         * иначе выполняем класс и метод который указан в массиве
         */
        if (gettype($routes[$this->request->getPathInfo()]) === "array") {
            $class = 'Imagenator\Main\Controllers\\' . $routes[$this->request->getPathInfo()][0];
            $controller = new $class();
            $this->response = $controller->{$routes[$this->request->getPathInfo()][1]}(new Response, $this->request);
        } else {
            $func = $routes[$this->request->getPathInfo()];
            $this->response = $func(new Response, $this->request);
        }

        $this->response = $this->response->end();
        echo $this->response['body'];
    }

    private function render($res){

    }

    public function addRoute(string $route, $controller)
    {
        //Устанавливаем контроллер
        $this->routes[$route] = $controller;
    }

    public function addRoutePost(string $route, $controller)
    {
        //Устанавливаем контроллер
        $this->routesPost[$route] = $controller;
    }
}