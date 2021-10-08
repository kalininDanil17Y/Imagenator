<?php
namespace App\Imagenator;

use App\Imagenator\Router;
use App\Imagenator\Database;
use Ramsey\Uuid\Uuid;

/**
 * Class App
 * @package App\Imagenator
 */
class App
{

    public function init()
    {
        /*
         * Соеденияемся с Базой
         */
        $database = new Database();
        $database->connect();

        /*
         * Создаём роутер
         */
        $router = new Router();

        /*
         * Указываем пути роутера
         */
        $router->addRoute('GET', '/', ['UploadController', 'showPage']);
        $router->addRoute('GET', '/upload', ['UploadController', 'uploadPage']);
        $router->addRoute('POST', '/upload', ['UploadController', 'upload']);
        $router->addRoute('POST', '/result', ['UploadController', 'resultPage']);

        $router->addRoute('GET', '/form', ['FirstController', 'form']);
        $router->addRoute('POST', '/form', ['FirstController', 'post']);
        $router->addRoute('GET', '/blablabla', ['SecondController', 'blablabla']);
        $router->addRoute('GET', '/hellYeah', function ($response, $request){
            $method = $request->query->get('method', 'print');
            if ($method !== "print") {
                return $response->redirect("https://google.com", 302);
            }
            $uuid = Uuid::uuid4();
            dump($uuid);
            dump($request);
            dump($_ENV);

        });

        $router->start();
    }
}