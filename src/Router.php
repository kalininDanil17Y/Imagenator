<?php
namespace Imagenator\Main;

use Symfony\Component\HttpFoundation\Request;

class Router
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
    }

    private function render($res){
        /*
         * Если ответ является массивом
         * то обрабатываем настройки
         */
        if (gettype($res) === "array") {
            /*
             * Если указан Code, устанавливаем его
             */
            if (array_key_exists("code", $res)) {
                http_response_code($res['code']);
            }

            /*
             * Если указаны заголовки, применяем их
             */
            if (!empty($res['header'])) {
                foreach ($res['header'] as $name => $value) {
                    header($name . ": " . $value);
                }
            }

            /*
             * Если есть ответ, печатаем его
             */
            if (array_key_exists("response", $res)) {
                echo $res['response'];
            }else if (array_key_exists("template", $res)) {
                $params = $res['templateParams'] ?? [];

                $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/Views/');
                $twig = new \Twig\Environment($loader);
                echo $twig->render($res['template'] . '.php', $params);
            }
        }else if(gettype($res) === "string") {
            /*
             * Печатаем текст
             */
            echo $res;
        }
    }

    public function Handle()
    {
        if ($this->method === "GET") {
            $this->methodGET();
        } else {
            $this->methodPOST();
        }

        $this->render($this->response);
    }

    private function methodGET(){
        /*
         * Если страницы нет, выводим 404
         */
        if(empty($this->routes[$this->request->getPathInfo()])){
            echo "404";
            die;
        }

        /*
         * Если контроллер является функцией, то выполняем её
         * иначе выполняем класс и метод который указан в массиве
         */
        if (gettype($this->routes[$this->request->getPathInfo()]) === "array") {
            $class = 'Imagenator\Main\Controllers\\' . $this->routes[$this->request->getPathInfo()][0];
            $controller = new $class();
            $this->response = $controller->{$this->routes[$this->request->getPathInfo()][1]}($this->request);
        } else {
            $func = $this->routes[$this->request->getPathInfo()];
            $this->response = $func($this->request);
        }
    }

    private function methodPOST(){
        /*
         * Если страницы нет, выводим 404
         */
        $this->routes = $this->routesPost;
        if(empty($this->routes[$this->request->getPathInfo()])){
            echo "404";
            die;
        }

        /*
         * Если контроллер является функцией, то выполняем её
         * иначе выполняем класс и метод который указан в массиве
         */
        if (gettype($this->routes[$this->request->getPathInfo()]) === "array") {
            $class = 'Imagenator\Main\Controllers\\' . $this->routes[$this->request->getPathInfo()][0];
            $controller = new $class();
            $this->response = $controller->{$this->routes[$this->request->getPathInfo()][1]}($this->request);
        } else {
            $func = $this->routes[$this->request->getPathInfo()];
            $this->response = $func($this->request);
        }
    }

    public function addRoute($route, $controller)
    {
        //Устанавливаем контроллер
        $this->routes[$route] = $controller;
    }

    public function addRoutePost($route, $controller)
    {
        //Устанавливаем контроллер
        $this->routesPost[$route] = $controller;
    }
}