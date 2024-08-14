<?php

declare (strict_types=1);

namespace Justas\CommissionTask\FileInput;

use Generator;
use InvalidArgumentException;

class CsvReader extends FileReader
{
    protected string $supportedFileExtension = 'csv';
    private string $delimiter;

    public function __construct(string $delimiter = DEFAULT_CSV_SEPARATOR)
    {
        $this->delimiter = $delimiter;
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
            $line = trim($line);

            $elements = explode($this->delimiter, $line);

            $result = [
                'date'          => $elements[0],
                'userID'        => $elements[1],
                'userType'      => $elements[2],
                'operationType' => $elements[3],
                'amount'        => $elements[4],
                'currency'      => $elements[5]
            ];

            yield (object) $result;
        }

        fclose($file);
    }
}