<?php

declare(strict_types=1);

use Justas\CommissionTask\CommissionCalculation\CommissionCalculator;
use Justas\CommissionTask\CommissionCalculation\PrivateDepositRule;
use Justas\CommissionTask\Operation\Operation;
use Justas\CommissionTask\Operation\OperationType;
use Justas\CommissionTask\Operation\UserOperationTracker;
use Justas\CommissionTask\User\UserType;
use PHPUnit\Framework\TestCase;

final class CommissionCalculatorTest extends TestCase
{
    private CommissionCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new CommissionCalculator(new UserOperationTracker());
    }

    public function testCalculatesSinglePrivateDepositCorrectly()
    {
        $operation = new Operation (
            new DateTime('2024-08-19'),
            1,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            100,
            'EUR'
        );

        $operationUSD = new Operation (
            new DateTime('2024-08-19'),
            1,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            100,
            'USD'
        );

        $commission = $this->calculator->calculateCommission($operation);
        $commissionUSD = $this->calculator->calculateCommission($operationUSD);

        $this->assertSame(0.03, $commission);
        $this->assertSame(0.03, $commissionUSD);
    }

    public function testCalculatesSinglePrivateWithdrawFree()
    {
        $operation = new Operation (
            new DateTime('2024-08-19'),
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            100,
            'EUR'
        );

        $operationUSD = new Operation (
            new DateTime('2024-08-19'),
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            100,
            'USD'
        );

        $operationJPY = new Operation (
            new DateTime('2024-08-19'),
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            100,
            'JPY'
        );


        $commission = $this->calculator->calculateCommission($operation);

        $this->assertSame(0.00, $commission);
        $this->assertSame(0.00, $commission);
        $this->assertSame(0.00, $commission);
    }

    public function testCalculatesPrivateWithdrawOverFreeAmount ()
    {
        $operation = new Operation (
            new DateTime('2024-08-19'),
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            1100,
            'EUR'
        );

        $commission = $this->calculator->calculateCommission($operation);

        $this->assertSame(0.30, $commission);
    }

    public function testCalculatesPrivateWithdrawOverFreeCount()
    {
        $operation = new Operation (
            new DateTime('2024-08-19'),
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            100,
            'EUR'
        );

        $commission1 = $this->calculator->calculateCommission($operation);
        $commission2 = $this->calculator->calculateCommission($operation);
        $commission3 = $this->calculator->calculateCommission($operation);
        $commission4 = $this->calculator->calculateCommission($operation);

        $this->assertSame(0.00, $commission1);
        $this->assertSame(0.00, $commission2);
        $this->assertSame(0.00, $commission3);
        $this->assertSame(0.30, $commission4);
    }

    public function testCalculatesSingleBusinessDepositCorrectly()
    {
        $operation = new Operation(
            new DateTime('2024-08-19'),
            1,
            UserType::BUSINESS,
            OperationType::DEPOSIT,
            100,
            'EUR'
        );

        $commission = $this->calculator->calculateCommission($operation);

        $this->assertSame(0.03, $commission);
    }

    public function testCalculatesSingleBusinessWithdrawCorrectly()
    {
        $operation = new Operation(
            new DateTime('2024-08-19'),
            1,
            UserType::BUSINESS,
            OperationType::WITHDRAW,
            100,
            'EUR'
        );

        $commission = $this->calculator->calculateCommission($operation);

        $this->assertSame(0.5, $commission);
    }
}