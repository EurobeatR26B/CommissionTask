<?php

declare (strict_types=1);

namespace Justas\CommissionTask;

use Dotenv\Dotenv;
use Justas\CommissionTask\CommissionCalculation\CommissionCalculator;
use Justas\CommissionTask\CurrencyConversion\ExchangeRateApiClient;
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

$csvReader = (new FileInput\CsvReader())->setFileName($argv[1]);

$operationParser = new OperationParser();

$exchangeRateApi = new ExchangeRateApiClient(useTestRates: true);
$userOperationTracker = new UserOperationTracker($exchangeRateApi);
$commissionCalculator = new CommissionCalculator($userOperationTracker);

foreach ($operationParser->parseFile($csvReader) as $operation)
{
   $commission = $commissionCalculator->calculateCommission($operation);

   echo $commission . PHP_EOL;
}