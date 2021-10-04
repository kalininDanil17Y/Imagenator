<?php
namespace Imagenator\Main\Controllers;

use Imagenator\Main\Response;

class IndexController
{
    public function form($req)
    {
        $resp = new Response();
        return $resp->view('form', [])
            ->setStatus(200)
            ->setHeader('Content-type', 'text/html;')
            ->end();
    }

    public function post($req)
    {
        $name = $req->request->get('name');
        return [
            'template' => 'index',
            'templateParams' => ['name' => $name]
        ];
    }
}