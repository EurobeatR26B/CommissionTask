<?php

declare (strict_types=1);

namespace Justas\CommissionTask;

require ("vendor/autoload.php");

$csv = new FileInput\CsvReader();
$csv->setFileName("input.csv");


foreach ($csv->getLine() as $line)
{
    var_dump($line);
    readline();
}