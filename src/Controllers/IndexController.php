<?php
namespace Imagenator\Main\Controllers;

class IndexController
{
    public function form($req)
    {
        /*
         * Основной код
         */

        return [
            'code' => 200,
            'header' => [
                'Content-type' => 'text/html;'
            ],
            'template' => 'form'
        ];

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