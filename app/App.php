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
        new Database();

        $rout = new Router();

        $rout->addRoute('/', ['UploadController', 'showPage']);
        $rout->addRoute('/upload', ['UploadController', 'uploadPage']);
        $rout->addRoutePost('/upload', ['UploadController', 'upload']);
        $rout->addRoutePost('/result', ['UploadController', 'result']);

        $rout->addRoute('/form', ['FirstController', 'form']);
        $rout->addRoutePost('/form', ['FirstController', 'post']);
        $rout->addRoute('/blablabla', ['SecondController', 'blablabla']);
        $rout->addRoute('/hellYeah', function ($response, $request){
            $method = $request->query->get('method', 'print');
            if ($method !== "print") {
                return $response->redirect("https://google.com", 302);
            }
            $uuid = Uuid::uuid4();
            dump($uuid);
            dump($request);

        });

        $rout->Handle();
    }
}