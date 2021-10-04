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
        $this->response['body'] = $responseHTML;
        return $this;
    }

    public function setHeader($headName, $headValue = '')
    {
        header($headName . ": " . $headValue);
        $this->response['header'][$headName] = $headValue;
        return $this;
    }

    public function setStatus($par1 = 200, $par2 = null){
        if ($par2 == null) {
            http_response_code($par1);
            $this->response['code'] = $par1; //Set code
        } else {
            http_response_code($par2);
            $this->response['body'] = $par1; //Set message
            $this->response['code'] = $par2; //Set code
        }
        return $this;
    }

    public function setBody($text){
        $this->response['body'] = $text;
        return $this;
    }

    public function end(){
        return $this->response;
    }
}