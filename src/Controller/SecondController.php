<?php
namespace Imagenator\Main\Controller;

/**
 * Class SecondController
 * @package Imagenator\Main\Controller
 */
class SecondController
{
    /**
     * @param $response
     * @param $request
     * @return mixed
     */
    public function blablabla($response, $request)
    {
        return $response->setBody("Hello))");
    }
}