<?php
namespace App\Imagenator;

use Twig\Loader\FilesystemLoader;
use \Twig\Environment;

class View
{
    private $twig;

    public function viewInit()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../templates/');
        $this->twig = new Environment($loader);
    }

    public function buildTemplate($templateName, $params = [])
    {
        return $this->twig->render($templateName . '.twig', $params);
    }
}