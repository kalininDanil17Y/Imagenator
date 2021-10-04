<?php
namespace Imagenator\Main;
use Imagenator\Main\Router;

class App
{
    public function init()
    {
        $rout = new Router();

        $rout->addRoute('/', ['FirstController', 'form']);
        $rout->addRoutePost('/', ['FirstController', 'post']);
        $rout->addRoute('/blablabla', ['SecondController', 'blablabla']);
        $rout->addRoute('/hellYeah', function ($response, $request){
            $method = $request->query->get('method', 'print');
            if ($method !== "print") {
                return $response->redirect("https://google.com", 400);
            }
            dump($method);
            dump($response);
            dump($request);
        });

        $rout->Handle();
    }
}