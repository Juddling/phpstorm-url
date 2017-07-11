<?php

use Juddling\PHPStorm\LaunchUrlCommand;
use PHPUnit\Framework\TestCase;

class LineNumberTest extends TestCase
{
    public function testLineNumber()
    {
        $launchCommand = new LaunchUrlCommand();
        $fileName = __DIR__ . "/resources/functionDefinition.php";
        $lineNumber = $launchCommand->lineNumber("someFunction", $fileName);
        $this->assertEquals(7, $lineNumber);
    }
}