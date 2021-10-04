<?php


namespace Imagenator\Main;
use Twig\Loader\FilesystemLoader;
use \Twig\Environment;

class View
{
    private $twig;

    public function init()
    {
        $loader = new FilesystemLoader(__DIR__ . '/Views/');
        $this->twig = new Environment($loader);
    }

    public function printTemplate($templateName, $params = [])
    {
        echo $this->twig->render($templateName . '.php', $params);
    }
}