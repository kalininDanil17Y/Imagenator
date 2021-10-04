<?php

$router->addRoute('/', ['IndexController', 'form']);
$router->addRoutePost('/', ['IndexController', 'post']);
$router->addRoute('/hellYeah', function ($req){
    dump($req);
});