<?php
namespace App\Imagenator;

use Twig\Loader\FilesystemLoader;
use \Twig\Environment;
use Twig\TwigFunction;

/**
 * Class View
 * @package App\Imagenator
 */
class View
{
    /**
     * @var
     */
    private $twig;

    /**
     * @param string $string
     * @return array|string
     */
    public static function _text(string $string = ''){
        $words = explode(".", $string);
        $langFile = __DIR__ . "/../translations/" . $words[0] . ".php";
        if (!file_exists($langFile)) {
            return $string;
        }
        $langarr = include($langFile);
        if (!is_array($langarr)) {
            return $string;
        }
        unset($words[0]);
        foreach ($words as $key) {
            if (is_array($langarr) && array_key_exists($key, $langarr)) {
                $langarr = $langarr[$key];
            } else {
                return $string;
            }
        }

        return $langarr;
    }

    /**
     *
     */
    public function viewInit()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../templates/');
        $this->twig = new Environment($loader);

        $_text = new TwigFunction('_text', function ($string = "") {
            return $this->_text($string);
        });
        $this->twig->addFunction($_text);
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