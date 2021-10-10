<?php
namespace Imagenator\Controller;

use phpDocumentor\Reflection\Types\Boolean;
use PhpParser\Node\Expr\Cast\Object_;

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

class I{
    public function isStudy(): bool
    {
        return true;
    }
    public function isWorking(): bool
    {
        return true;
    }
    public function happy()
    {

    }
}