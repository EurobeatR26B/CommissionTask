<?php

declare(strict_types=1);

namespace Justas\CommissionTask\FileInput;

use InvalidArgumentException;

final class ArgumentValidator
{
    private static ArgumentValidator $argumentValidator;

    private function __construct()
    {

    }

    public static function getInstance(): self
    {
        if (!isset(self::$argumentValidator)) {
            self::$argumentValidator = new static();
        }

        return self::$argumentValidator;
    }

    public function validateLaunchArguments(array $args): void
    {
        $inputFile = $args[1];

        if (!isset($inputFile) || $inputFile == '') {
            throw new InvalidArgumentException("No input file has been provided.");
        }

        if (!file_exists($inputFile)) {
            throw new InvalidArgumentException("The input file '$inputFile' could not be found in the project directory.");
        }

        $inputFileExtension = pathinfo($inputFile, PATHINFO_EXTENSION);

        if (!in_array($inputFileExtension, SUPPORTED_INPUT_FILE_EXTENSIONS)) {
            $errorMessage = "Unsupported file format. Please use one of the following: " .
            implode(', ', SUPPORTED_INPUT_FILE_EXTENSIONS);

            throw new InvalidArgumentException($errorMessage);
        }

    }
}
