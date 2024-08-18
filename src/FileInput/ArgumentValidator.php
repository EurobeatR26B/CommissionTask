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

    public function validateLaunchArguments($args): void
    {
        if (!isset($args[1])) {
            throw new InvalidArgumentException("No input file has been provided.");
        }

        if (!file_exists($args[1])) {
            throw new InvalidArgumentException("The input file could not be found in the project directory.");
        }

        if (!in_array(pathinfo($args[1], PATHINFO_EXTENSION), SUPPORTED_INPUT_FILE_EXTENSIONS)) {
            $errorMessage = "Unsupported file format. Please use one of the following: " .
            implode(', ', SUPPORTED_INPUT_FILE_EXTENSIONS);

            throw new InvalidArgumentException($errorMessage);
        }

    }
}
