<?php
namespace Imagenator\Main\Controllers;

class SecondController
{
    public function blablabla($response, $request)
    {
        return $response->setBody("Hello))");
    }
}