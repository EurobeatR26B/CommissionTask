<?php

declare (strict_types=1);

namespace Justas\CommissionTask;

use Justas\CommissionTask\Operation\OperationParser;

require ("vendor/autoload.php");


$csv = new FileInput\CsvReader();
$csv->setFileName("input.csv");

$operationParser = new OperationParser($csv);
$operationParser->parseFile();