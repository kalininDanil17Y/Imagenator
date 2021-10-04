<?php
namespace Imagenator\Main\Controllers;

class IndexController
{
    public function form($response, $req)
    {
        return $response->view('form')
            ->setStatus(200)
            ->setHeader('Content-type', 'text/html;')
            ->end();
    }

    public function post($response, $req)
    {
        $name = $req->request->get('name');
        return $response->view('index', ['name' => $name])
            ->setStatus(200)
            ->setHeader('Content-type', 'text/html;')
            ->end();
    }
}