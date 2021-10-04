<?php
namespace Imagenator\Main;
use Imagenator\Main\Router;

class App
{
    public function init()
    {
        $rout = new Router();

        $rout->addRoute('/', ['IndexController', 'form']);
        $rout->addRoutePost('/', ['IndexController', 'post']);
        $rout->addRoute('/hellYeah', function ($req){
            dump($req);
        });

        $rout->Handle();


    }
}