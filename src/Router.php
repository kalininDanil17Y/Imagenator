<?php
namespace Imagenator\Main;

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
            $this->response = $controller->{$routes[$this->request->getPathInfo()][1]}($this->request);
        } else {
            $func = $routes[$this->request->getPathInfo()];
            $this->response = $func($this->request);
        }

        /*
         * Если ответ является массивом
         * то обрабатываем настройки
         */
        if (gettype($this->response) === "array") {
            /*
             * Если указан Code, устанавливаем его
             */
            if (array_key_exists("code", $this->response)) {
                http_response_code($this->response['code']);
            }

            /*
             * Если указаны заголовки, применяем их
             */
            if (!empty($this->response['header'])) {
                foreach ($this->response['header'] as $name => $value) {
                    header($name . ": " . $value);
                }
            }

            /*
             * Если есть ответ, печатаем его
             */
            if (array_key_exists("response", $this->response)) {
                echo $this->response['response'];
            } else if (array_key_exists("template", $this->response)) {
                $params = $this->response['templateParams'] ?? [];
                $this->printTemplate($this->response['template'], $params);
            }

        }else if(gettype($this->response) === "string") {
            echo $this->response;
        }
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