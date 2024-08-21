<?php

declare(strict_types=1);

use Justas\CommissionTask\CommissionCalculation\CommissionCalculator;
use Justas\CommissionTask\FileInput\CsvReader;
use Justas\CommissionTask\Operation\OperationParser;
use Justas\CommissionTask\Operation\UserOperationTracker;
use PHPUnit\Framework\TestCase;

class MainTest extends TestCase
{
    public function testGetsSameResultsAsTheTaskExample()
    {
        $csvReader = (new CsvReader())->setFileName('taskInput.csv');

        $operationParser = new OperationParser();
        $userOperationTracker = new UserOperationTracker();
        $commissionCalculator = new CommissionCalculator($userOperationTracker);

        $resultLine = "";

        foreach ($csvReader->getLine() as $line) {
            $operation = $operationParser->parseSingleLine($line);
            $commission = $commissionCalculator->calculateCommission($operation);

            if (!in_array($operation->getCurrency(), CURRENCIES_WITH_NO_DECIMALS)) {
                $commission = number_format($commission, 2);
            }

            $resultLine .= $commission . PHP_EOL;
        }

        $expectedResultLine =
        "0.60
        3.00
        0.00
        0.06
        1.50
        0
        0.70
        0.30
        0.30
        3.00
        0.00
        0.00
        8612
        ";

        $expectedResultLine = str_replace(' ', '', $expectedResultLine);

        $this->assertSame($expectedResultLine, $resultLine);
    }
}
