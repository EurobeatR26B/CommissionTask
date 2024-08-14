<?php

declare(strict_types=1);

namespace Justas\CommissionTask\CommissionCalculation;

use Justas\CommissionTask\Operation\Operation;
use Justas\CommissionTask\Operation\UserOperationTracker;

class PrivateWithdrawRule implements CommissionRuleInterface
{
    private UserOperationTracker $userOperationTracker;

    public function __construct(UserOperationTracker $userOperationTracker)
    {
        $this->userOperationTracker = $userOperationTracker;
    }

    public function calculate(Operation $operation): float
    {
        if ($this->userOperationTracker->isOperationEligibleForFreeCommission($operation))
        {
            return 0.00;
        }

        $userOperationsSum = $this->userOperationTracker->getUserOperationSumThisPeriod($operation);
        $taxableOperationAmount = abs($operation->getAmount() - $userOperationsSum);

        $commission = $taxableOperationAmount * COMMISSION_PRIVATE_WITHDRAW;
        $commission = round($commission, COMMISSION_ROUNDING_PRECISION);

        return $commission;
    }
}