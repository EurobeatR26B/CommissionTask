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

    public function testSinglePrivateDepositCorrectly()
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

    public function testSinglePrivateWithdrawFreeAmount()
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

    public function testSingleBusinessDepositCorrectly()
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

    public function testSingleBusinessWithdrawCorrectly()
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

    public function testPrivateWithdrawOverFreeAmount ()
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

    public function testPrivateWithdrawOverFreeCount()
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

    public function testPrivateWithdrawUnderFreeCount()
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

        $this->assertSame(0.00, $commission1);
        $this->assertSame(0.00, $commission2);
        $this->assertSame(0.00, $commission3);
    }    

    public function testPrivateWithdrawOtherCurrencyUsd()
    {
        $operation = new Operation (
            new DateTime('2024-08-19'),
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            1100,
            'USD'
        );

        $commission = $this->calculator->calculateCommission($operation);

        $this->assertSame(0.00, $commission);
    }

    public function testPrivateWithdrawOtherCurrencyJpySingleOverFree()
    {
        $operation = new Operation (
            new DateTime('2024-08-19'),
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            1000000,
            'JPY'
        );

        $commission = $this->calculator->calculateCommission($operation);

        $this->assertSame(2612.0, $commission);
    }

    public function testPrivateWithdrawOtherCurrencyJpyFree()
    {
        $operation = new Operation (
            new DateTime('2024-08-19'),
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            129530,
            'JPY'
        );

        $commission = $this->calculator->calculateCommission($operation);

        $this->assertSame(0.0, $commission);
    }

    public function testPrivateWithdrawOtherCurrencyJpyFull()
    {
        $operation = new Operation (
            new DateTime('2024-08-19'),
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            129530,
            'JPY'
        );

        $operation2 = new Operation (
            new DateTime('2024-08-19'),
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            1000000,
            'JPY'
        );

        $commission = $this->calculator->calculateCommission($operation);
        $commission2 = $this->calculator->calculateCommission($operation2);

        $this->assertSame(0.0, $commission);
        $this->assertSame(3000.0, $commission2);
    }

    public function testCalculatePrivateWithdrawMixed()
    {
        $operation = new Operation (
            new DateTime('2024-08-19'),
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            100,
            'EUR'
        );

        $operation2 = new Operation (
            new DateTime('2024-08-19'),
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            200,
            'EUR'
        );

        $operation3 = new Operation (
            new DateTime('2024-08-19'),
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            300,
            'EUR'
        );

        $operation4 = new Operation (
            new DateTime('2024-08-19'),
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            1000,
            'EUR'
        );

        $operation5 = new Operation (
            new DateTime('2024-08-19'),
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            1000000,
            'JPY'
        );

        $operation6 = new Operation (
            new DateTime('2024-08-19'),
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            1000,
            'USD'
        );

        $operation7 = new Operation (
            new DateTime('2024-08-30'),
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            1000,
            'EUR'
        );

        $commission = $this->calculator->calculateCommission($operation);
        $commission2 = $this->calculator->calculateCommission($operation2);
        $commission3 = $this->calculator->calculateCommission($operation3);
        $commission4 = $this->calculator->calculateCommission($operation4);
        $commission5 = $this->calculator->calculateCommission($operation5);
        $commission6 = $this->calculator->calculateCommission($operation6);
        $commission7 = $this->calculator->calculateCommission($operation7);

        $this->assertSame(0.00, $commission);
        $this->assertSame(0.00, $commission2);
        $this->assertSame(0.00, $commission3);
        $this->assertSame(3.00, $commission4);
        $this->assertSame(3000.0, $commission5);
        $this->assertSame(3.00, $commission6);
        $this->assertSame(0.00, $commission7);
    }
}