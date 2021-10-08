<?php
namespace Hexlet\Phpunit\Tests;

use PHPUnit\Framework\TestCase;
use App\Imagenator\View;

class ViewTest extends TestCase
{
    public function testText()
    {
        $this->assertEquals('', View::_text(''));
        $this->assertEquals('', View::_text());
        $this->assertEquals('bla', View::_text('bla'));
        $this->assertEquals('Hello World', View::_text('ru.hello.text1'));
    }
}