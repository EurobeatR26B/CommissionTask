<?php

declare (strict_types=1);

namespace Justas\CommissionTask;

use Dotenv\Dotenv;
use Justas\CommissionTask\CommissionCalculation\CommissionCalculator;
use Justas\CommissionTask\Operation\Operation;
use Justas\CommissionTask\Operation\OperationParser;
use Justas\CommissionTask\Operation\UserOperationTracker;

require ("vendor/autoload.php");
require ("config.php");

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$csv = new FileInput\CsvReader();
$csv->setFileName("input.csv");

$operationParser = new OperationParser($csv);
$operationRepository = $operationParser->parseFile();


$userOperationTracker = new UserOperationTracker();
$commissionCalculator = new CommissionCalculator($userOperationTracker);

$users = array_keys($operationRepository->getAll());
foreach ($csv->getLine() as $line)
{
   $operation = $operationParser->parseSingleLine($line);
   $commission = $commissionCalculator->calculateCommission($operation);

   echo "Result: $commission - $operation" . PHP_EOL;
}