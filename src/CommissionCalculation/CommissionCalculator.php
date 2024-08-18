<?php

declare(strict_types=1);

namespace Justas\CommissionTask\CommissionCalculation;

use Justas\CommissionTask\Operation\Operation;
use Justas\CommissionTask\Operation\OperationType;
use Justas\CommissionTask\Operation\UserOperationTracker;
use Justas\CommissionTask\User\UserType;
use PHPUnit\Framework\Constraint\Operator;

class CommissionCalculator
{
    public function __construct(
        private UserOperationTracker $operationTracker
    ){}

    public function calculateCommission(Operation $operation)
    {        
        $commissionRule = $this->getCommissionRule($operation);

        echo "Operation detected as " . $commissionRule::class . ' (' . $operation->__toString() . ')' . PHP_EOL;

        $commissionAmount = $commissionRule->calculate($operation, $this->operationTracker);
        $this->operationTracker->addCompletedOperation($operation);

        // echo "Calculating for " . $operation->__toString() . '...' . PHP_EOL;
        // $this->operationTracker->addCompletedOperation($operation);

        // $operationCount = $this->operationTracker->getUserOperationCountThisPeriod($operation);
        // $operationAmount = $this->operationTracker->getUserOperationSumThisPeriod($operation);
        // echo "So far, this user has done " . $operationCount . " operations this week, totaling " . $operationAmount . " EUR..." . PHP_EOL;

        return $commissionAmount;
    }

    private function getCommissionRule (Operation $operation)
    {
        $commissionRule = match ($operation->getUserType())
        {
            UserType::PRIVATE => match ($operation->getOperationType())
            {
                OperationType::DEPOSIT => new PrivateDepositRule(),
                OperationType::WITHDRAW => new PrivateWithdrawRule($this->operationTracker)
            },
            UserType::BUSINESS => match ($operation->getOperationType())
            {
                OperationType::DEPOSIT => new BusinessDepositRule(),
                OperationType::WITHDRAW => new BusinessWithdrawRule()
            },
        };

        return $commissionRule;
    }
}