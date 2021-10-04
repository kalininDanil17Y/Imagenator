<?php
namespace Imagenator\Main\Controllers;

class FirstController
{
    public function form($response, $request)
    {
        return $response->view('form')
            ->setStatus(200)
            ->setHeader('Content-type', 'text/html;');
    }

    public function post($response, $request)
    {
        $name = $request->request->get('name');
        return $response->view('index', ['name' => $name])
            ->setStatus(202);
    }
}