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

        $this->operationTracker->addCompletedOperation($operation);

        return $commissionRule->calculate($operation, $this->operationTracker);


        // echo "Calculating for " . $operation->__toString() . '...' . PHP_EOL;
        // $this->operationTracker->addCompletedOperation($operation);

        // $operationCount = $this->operationTracker->getUserOperationCountThisPeriod($operation);
        // $operationAmount = $this->operationTracker->getUserOperationSumThisPeriod($operation);
        // echo "So far, this user has done " . $operationCount . " operations this week, totaling " . $operationAmount . " EUR..." . PHP_EOL;
    }
}