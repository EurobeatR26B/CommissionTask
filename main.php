<?php

declare (strict_types=1);

namespace Justas\CommissionTask;

use Dotenv\Dotenv;
use Justas\CommissionTask\CommissionCalculation\CommissionCalculator;
use Justas\CommissionTask\FileInput\ArgumentValidator;
use Justas\CommissionTask\Operation\Operation;
use Justas\CommissionTask\Operation\OperationParser;
use Justas\CommissionTask\Operation\UserOperationTracker;

require ("vendor/autoload.php");
require ("config.php");

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$argumentValidator = ArgumentValidator::getInstance();
$argumentValidator->validateLaunchArguments($argv);

$csvReader = new FileInput\CsvReader();
$csvReader->setFileName($argv[1]);

$operationParser = new OperationParser();
$operationRepository = $operationParser->parseFile($csvReader);


$userOperationTracker = new UserOperationTracker();
$commissionCalculator = new CommissionCalculator($userOperationTracker);

$users = array_keys($operationRepository->getAll());
foreach ($csvReader->getLine() as $line)
{
   $operation = $operationParser->parseSingleLine($line);
   $commission = $commissionCalculator->calculateCommission($operation);

   echo "Result: $commission - $operation" . PHP_EOL;
}