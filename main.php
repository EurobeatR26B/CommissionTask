<?php

declare (strict_types=1);

namespace Justas\CommissionTask;

use Justas\CommissionTask\CommissionCalculation\CommissionCalculator;
use Justas\CommissionTask\Operation\OperationParser;
use Justas\CommissionTask\Operation\UserOperationTracker;

require ("vendor/autoload.php");
require ("config.php");


$csv = new FileInput\CsvReader();
$csv->setFileName("input.csv");

$operationParser = new OperationParser($csv);
$operationRepository = $operationParser->parseFile();


$userOperationTracker = new UserOperationTracker();
$commissionCalculator = new CommissionCalculator($userOperationTracker);

$users = array_keys($operationRepository->getAll());
sort($users);

foreach ($users as $key)
{
   foreach ($operationRepository->getOperationsByUser($key) as $operation)
   {
        $commissionCalculator->calculateCommission($operation);
        echo PHP_EOL;
   }
}

