<?php
namespace App\Imagenator;

use App\Imagenator\Router;
use App\Imagenator\Database;
use Ramsey\Uuid\Uuid;
use Dotenv\Dotenv;

/**
 * Class App
 * @package App\Imagenator
 */
class App
{

    public function init()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . "/../");
        $dotenv->load();

        new Database();

        $router = new Router();

        $router->addRoute('/', ['UploadController', 'showPage']);
        $router->addRoute('/upload', ['UploadController', 'uploadPage']);
        $router->addRoutePost('/upload', ['UploadController', 'upload']);
        $router->addRoutePost('/result', ['UploadController', 'resultPage']);

        $router->addRoute('/form', ['FirstController', 'form']);
        $router->addRoutePost('/form', ['FirstController', 'post']);
        $router->addRoute('/blablabla', ['SecondController', 'blablabla']);
        $router->addRoute('/hellYeah', function ($response, $request){
            $method = $request->query->get('method', 'print');
            if ($method !== "print") {
                return $response->redirect("https://google.com", 302);
            }
            $uuid = Uuid::uuid4();
            dump($uuid);
            dump($request);
            dump($_ENV);

        });

        $router->Handle();
    }
}