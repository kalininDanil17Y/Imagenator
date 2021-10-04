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

        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/Views/');
        $this->twig = new \Twig\Environment($loader);
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
            } else if (array_key_exists("template", $res)) {
                $params = $res['templateParams'] ?? [];


                echo $this->twig->render($res['template'] . '.php', $params);
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
            $routes = $this->routes;
        } else {
            $routes = $this->routesPost;
        }

        /*
         * Если страницы нет, выводим 404
         */
        if (empty($routes[$this->request->getPathInfo()])) {
            echo $this->twig->render('errors/404.php', []);
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

        $this->render($this->response);
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