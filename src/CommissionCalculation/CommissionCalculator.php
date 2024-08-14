<?php

declare(strict_types=1);

namespace Justas\CommissionTask\CommissionCalculation;

use Justas\CommissionTask\Operation\Operation;
use Justas\CommissionTask\Operation\OperationType;
use Justas\CommissionTask\Operation\UserOperationTracker;
use Justas\CommissionTask\User\UserType;

class CommissionCalculator
{
    public function __construct(
        private UserOperationTracker $operationTracker
    ){}

    public function calculateCommission(Operation $operation)
    {        
        $commissionRule = match ($operation->getOperationType())
        {
            UserType::PRIVATE => match ($operation->getOperationType())
            {
                OperationType::DEPOSIT => new PrivateDepositRule(),
                OperationType::WITHDRAW => new PrivateDepositRule()
            },
            UserType::BUSINESS => match ($operation->getOperationType())
            {
                OperationType::DEPOSIT => new BusinessDepositRule(),
                OperationType::WITHDRAW => new BusinessWithdrawRule()
            },
        };

        return $commissionRule->calculate($operation, $operationTracker);


        echo "Calculating for " . $operation->__toString() . '...' . PHP_EOL;
        $this->operationTracker->addCompletedOperation($operation);

        $operationCount = $this->operationTracker->getUserOperationCountThisWeek($operation);
        $operationAmount = $this->operationTracker->getUserOperationSumThisWeek($operation);
        echo "So far, this user has done " . $operationCount . " operations this week, totaling " . $operationAmount . " EUR..." . PHP_EOL;
    }
}