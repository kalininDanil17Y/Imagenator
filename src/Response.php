<?php
namespace Imagenator\Main;

class Response extends View
{
    private $response = [];

    public function __construct()
    {
        $this->init();
        return $this;
    }

    public function view($templateName, $params = [])
    {
        $responseHTML = $this->printTemplate($templateName, $params);
        return $this;
    }

    public function setHeader($headName, $headValue = '')
    {
        $this->response['header'][$headName] = $headValue;
        return $this;
    }

    public function setStatus($par1 = 200, $par2 = null){
        if ($par2 == null) {
            $this->response['code'] = $par1; //Set code
        } else {
            $this->response['response'] = $par1; //Set message
            $this->response['code'] = $par2; //Set code
        }
        return $this;
    }

    public function end(){
        return $this->response;
    }
}