<?php

declare (strict_types=1);

namespace Justas\CommissionTask\FileInput;

use Generator;
use InvalidArgumentException;

class CsvReader extends FileReader
{
    protected string $supportedFileExtension = 'csv';

    public function __construct()
    {
        
    }

    public function getLine(): Generator
    {
        $file = $this->validateFileCanBeOpened();
        if (!$file)
        {
            $message = "The input file could not be opened";
            throw new InvalidArgumentException($message);
        }

        while (!feof($file))
        {
            $line = fgets($file);

            $elements = explode(',', $line);

            yield $elements;
        }
    }
}