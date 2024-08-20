<?php

declare(strict_types=1);

use Justas\CommissionTask\Operation\Operation;
use Justas\CommissionTask\Operation\OperationType;
use Justas\CommissionTask\Operation\UserOperationTracker;
use Justas\CommissionTask\User\UserType;
use PHPUnit\Framework\TestCase;

class UserOperationTrackerTest extends TestCase
{
    private UserOperationTracker $tracker;

    protected function setUp(): void
    {
        $this->tracker = new UserOperationTracker();
    }

    public function testAddingOperation()
    {
        $operation = new Operation(
            new DateTime('2024-08-20'),
            1,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            1.00,
            'EUR'
        );

        $count = $this->tracker->getUserOperationCountThisPeriod($operation);
        $this->assertSame(0, $count);

        $this->tracker->addCompletedOperation($operation);

        $count = $this->tracker->getUserOperationCountThisPeriod($operation);
        $this->assertSame(1, $count);
    }

    public function testGettingOperationCountSamePeriod()
    {
        $operation = new Operation(
            new DateTime('2024-08-20'),
            1,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            1.00,
            'EUR'
        );

        $this->tracker->addCompletedOperation($operation);
        $this->tracker->addCompletedOperation($operation);
        $this->tracker->addCompletedOperation($operation);
        $this->tracker->addCompletedOperation($operation);
        $this->tracker->addCompletedOperation($operation);

        $count = $this->tracker->getUserOperationCountThisPeriod($operation);
        $this->assertSame(5, $count);
    }

    public function testGettingOperationCountDifferentPeriod()
    {
        $operation = new Operation(
            new DateTime('2024-08-01'),
            1,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            1.00,
            'EUR'
        );

        $operation2 = new Operation(
            new DateTime('2024-08-10'),
            1,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            1.00,
            'EUR'
        );

        $operation3 = new Operation(
            new DateTime('2024-08-20'),
            1,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            1.00,
            'EUR'
        );

        $this->tracker->addCompletedOperation($operation);
        $this->tracker->addCompletedOperation($operation2);
        $this->tracker->addCompletedOperation($operation3);

        $count = $this->tracker->getUserOperationCountThisPeriod($operation);
        $count2 = $this->tracker->getUserOperationCountThisPeriod($operation2);
        $count3 = $this->tracker->getUserOperationCountThisPeriod($operation3);
        
        $this->assertSame(1, $count);
        $this->assertSame(1, $count);
        $this->assertSame(1, $count);
    }

    public function testGettingOperationSumSamePeriod()
    {
        $operation = new Operation(
            new DateTime('2024-08-20'),
            1,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            1.00,
            'EUR'
        );

        $this->tracker->addCompletedOperation($operation);
        $this->tracker->addCompletedOperation($operation);
        $this->tracker->addCompletedOperation($operation);
        $this->tracker->addCompletedOperation($operation);
        $this->tracker->addCompletedOperation($operation);

        $count = $this->tracker->getUserOperationSumThisPeriod($operation);
        $this->assertSame(5.0, $count);
    }

    public function testGettingOperationSumDifferentPeriod()
    {
        $operation = new Operation(
            new DateTime('2024-08-01'),
            1,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            1.00,
            'EUR'
        );

        $operation2 = new Operation(
            new DateTime('2024-08-10'),
            1,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            1.01,
            'EUR'
        );

        $operation3 = new Operation(
            new DateTime('2024-08-20'),
            1,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            1.00,
            'EUR'
        );

        $this->tracker->addCompletedOperation($operation);
        $this->tracker->addCompletedOperation($operation2);
        $this->tracker->addCompletedOperation($operation3);

        $count = $this->tracker->getUserOperationSumThisPeriod($operation);
        $count2 = $this->tracker->getUserOperationSumThisPeriod($operation2);
        $count3 = $this->tracker->getUserOperationSumThisPeriod($operation3);
        
        $this->assertSame(1.00, $count);
        $this->assertSame(1.01, $count2);
        $this->assertSame(1.00, $count3);
    }

    public function testIsUserEligibleForFreeCommissionPrivateWithdraw()
    {
        $operation = new Operation(
            new DateTime('2024-08-01'),
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            1.00,
            'EUR'
        );

        $eligible = $this->tracker->isOperationEligibleForFreeCommission($operation);
        $this->assertTrue($eligible);

        $this->tracker->addCompletedOperation($operation);
        $eligible = $this->tracker->isOperationEligibleForFreeCommission($operation);

        $this->assertTrue($eligible);

        $operation2 = new Operation(
            new DateTime('2024-08-01'),
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            998.00,
            'EUR'
        );

        $this->tracker->addCompletedOperation($operation2);
        $eligible = $this->tracker->isOperationEligibleForFreeCommission($operation2);
        $this->assertTrue($eligible);

        $this->tracker->addCompletedOperation($operation);
        $eligible = $this->tracker->isOperationEligibleForFreeCommission($operation2);
        $this->assertTrue($eligible);
    }

    public function testIsUserEligibleForFreeCommissionPrivateWithdrawCount()
    {
        $operation = new Operation(
            new DateTime('2024-08-01'),
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            1.00,
            'EUR'
        );

        $this->tracker->addCompletedOperation($operation);
        $this->tracker->addCompletedOperation($operation);
        $this->tracker->addCompletedOperation($operation);
        $eligible = $this->tracker->isOperationEligibleForFreeCommission($operation);
        $this->assertTrue($eligible);

        $this->tracker->addCompletedOperation($operation);
        
        $eligible = $this->tracker->isOperationEligibleForFreeCommission($operation);
        $this->assertFalse($eligible);
    }

    public function testIsUserEligibleForFreeCommissionPrivateWithdrawSum()
    {
        $operation = new Operation(
            new DateTime('2024-08-01'),
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            334.00,
            'EUR'
        );

        $this->tracker->addCompletedOperation($operation);
        $eligible = $this->tracker->isOperationEligibleForFreeCommission($operation);
        $this->assertTrue($eligible);

        $this->tracker->addCompletedOperation($operation);
        $eligible = $this->tracker->isOperationEligibleForFreeCommission($operation);
        $this->assertTrue($eligible);

        $this->tracker->addCompletedOperation($operation);
        $eligible = $this->tracker->isOperationEligibleForFreeCommission($operation);
        $this->assertFalse($eligible);
    }

    public function testIsUserEligibleForFreeCommissionPrivateWithdrawSumOtherCurrency()
    {
        $operation = new Operation(
            new DateTime('2024-08-01'),
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            500,
            'USD'
        );

        $this->tracker->addCompletedOperation($operation);
        $eligible = $this->tracker->isOperationEligibleForFreeCommission($operation);
        $this->assertTrue($eligible);

        $this->tracker->addCompletedOperation($operation);
        $eligible = $this->tracker->isOperationEligibleForFreeCommission($operation);
        $this->assertTrue($eligible);

        $this->tracker->addCompletedOperation($operation);
        $eligible = $this->tracker->isOperationEligibleForFreeCommission($operation);
        $this->assertFalse($eligible);
    }

    public function testIsUserEligibleForFreeCommissionPrivateDeposit()
    {
        $operation = new Operation(
            new DateTime('2024-08-01'),
            1,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            1.00,
            'EUR'
        );

        $eligible = $this->tracker->isOperationEligibleForFreeCommission($operation);
        $this->assertFalse($eligible);
    }

    public function testIsUserEligibleForFreeCommissionBusinessWithdraw()
    {
        $operation = new Operation(
            new DateTime('2024-08-01'),
            1,
            UserType::BUSINESS,
            OperationType::WITHDRAW,
            1.00,
            'EUR'
        );

        $eligible = $this->tracker->isOperationEligibleForFreeCommission($operation);
        $this->assertFalse($eligible);
    }

    public function testIsUserEligibleForFreeCommissionBusinessDeposit()
    {
        $operation = new Operation(
            new DateTime('2024-08-01'),
            1,
            UserType::BUSINESS,
            OperationType::DEPOSIT,
            1.00,
            'EUR'
        );

        $eligible = $this->tracker->isOperationEligibleForFreeCommission($operation);
        $this->assertFalse($eligible);
    }
}