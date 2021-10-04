<?php


namespace Imagenator\Main;
use Twig\Loader\FilesystemLoader;
use \Twig\Environment;

class View
{
    private $twig;

    public function viewInit()
    {
        $loader = new FilesystemLoader(__DIR__ . '/Views/');
        $this->twig = new Environment($loader);
    }

    public function buildTemplate($templateName, $params = [])
    {
        return $this->twig->render($templateName . '.twig', $params);
    }
}