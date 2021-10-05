<?php
namespace Imagenator\Main\Controller;

class SecondController
{
    public function blablabla($response, $request)
    {
        return $response->setBody("Hello))");
    }
}