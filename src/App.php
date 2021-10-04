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
            dump($response);
            dump($request);
        });

        $rout->Handle();
    }
}