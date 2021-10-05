<?php
namespace App\Imagenator;

use Twig\Loader\FilesystemLoader;
use \Twig\Environment;

/**
 * Class View
 * @package App\Imagenator
 */
class View
{
    private $twig;

    public function viewInit()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../templates/');
        $this->twig = new Environment($loader);
    }

    /**
     * @param $templateName
     * @param array $params
     * @return mixed
     */
    public function buildTemplate($templateName, $params = [])
    {
        return $this->twig->render($templateName . '.twig', $params);
    }
}