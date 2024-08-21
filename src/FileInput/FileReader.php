<?php

declare (strict_types=1);

namespace Justas\CommissionTask\FileInput;

use InvalidArgumentException;

abstract class FileReader
{
    protected $fileName;
    protected string $supportedFileExtension = "";

    abstract public function getLine();

    protected function validateFileExtension(string $fileName)
    {
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        return $fileExtension === $this->supportedFileExtension;
    }

    protected function validateFileCanBeOpened()
    {
        if (!isset($this->fileName)) {
            $message = "No file has been provided to read from";
            throw new InvalidArgumentException($message);
        }

        $file = fopen($this->fileName, "r");

        return $file;
    }

    public function setFileName(string $fileName): self
    {
        if ($this->validateFileExtension($fileName)) {
            $this->fileName = $fileName;
            return $this;
        } else {
            $message = sprintf("Provided file is not %s", $this->supportedFileExtension);
            throw new InvalidArgumentException($message);
        }
    }

    public function getFileName()
    {
        return $this->fileName ?? null;
    }
}
