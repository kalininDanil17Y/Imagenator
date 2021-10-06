<?php
namespace Imagenator\Controller;

/**
 * Class FirstController
 * @package Imagenator\Main\Controller
 */
class FirstController
{
    /**
     * @param $response
     * @param $request
     * @return mixed
     */
    public function form($response, $request)
    {
        return $response->view('form')
            ->setStatus(201)
            ->setHeader('Content-type', 'text/html;');
    }

    /**
     * @param $response
     * @param $request
     * @return mixed
     */
    public function post($response, $request)
    {
        $name = $request->request->get('name');
        return $response->view('index', ['name' => $name])
            ->setStatus(202);
    }
}