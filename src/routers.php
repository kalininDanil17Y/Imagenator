<?php

$router->addRoute('/', ['IndexController', 'form']);
$router->addRoutePost('/', ['IndexController', 'post']);
