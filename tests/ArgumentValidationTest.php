<?php

declare(strict_types=1);

require('config.php');

use PHPUnit\Framework\TestCase;
use Psalm\Issue\InvalidArgument;
use Justas\CommissionTask\FileInput\ArgumentValidator;
use Justas\CommissionTask\FileInput\CsvReader;

final class ArgumentValidationTest extends TestCase
{
    public function testDetectsEmptyArguments()
    {
        $args = [];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("No input file has been provided.");

        ArgumentValidator::getInstance()->validateLaunchArguments($args);
    }

    public function testDetectsNoInputFile()
    {
        $args = ["script.php"];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("No input file has been provided.");

        ArgumentValidator::getInstance()->validateLaunchArguments($args);
    }

    public function testFileCannotBeFound()
    {
        $args = ["script.php", "ghostfile.txt"];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The input file could not be found in the project directory.");

        ArgumentValidator::getInstance()->validateLaunchArguments($args);
    }

    public function testDetectsUnsupportedExtensions()
    {
        $fileNames = ["file.a", "file.exe", "file.csv", "file.log", "file.log", "file.pdf", "file.doc", "file.txt"];

        foreach ($fileNames as $file) {
            $args = ["script.php", $file];

            try {
                ArgumentValidator::getInstance()->validateLaunchArguments($args);
            } catch (Exception $e) {
                $this->assertSame($e::class, InvalidArgumentException::class);
            }
        }
    }

    public function testAcceptsSupportedFileFormat()
    {
        $args = ["script.php", "input.csv"];

        ArgumentValidator::getInstance()->validateLaunchArguments($args);
        $reader = new CsvReader($args[1]);

        $this->assertIsObject($reader);
        $this->assertSame($reader::class, CsvReader::class);
    }
}
