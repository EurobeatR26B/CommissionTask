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
        $taxableOperationAmount = $operation->getAmount();

        if ($this->userOperationTracker->isOperationEligibleForFreeCommission($operation))
        {
            $taxableOperationAmount = $this->getTaxableAmount($operation);
        }
        // else {
        //     $userOperationsSum = $this->userOperationTracker->getUserOperationSumThisPeriod($operation);
        //     $taxableOperationAmount = abs($operation->getAmount() - $userOperationsSum);
        // }

        echo "Calculating commission for " . $operation->__toString() . PHP_EOL;
        $commission = $taxableOperationAmount * COMMISSION_PRIVATE_WITHDRAW;
        $commission = round($commission, COMMISSION_ROUNDING_PRECISION);

        echo '$comission = ' . $taxableOperationAmount . ' * ' . COMMISSION_PRIVATE_WITHDRAW . ' = ' . $commission . PHP_EOL;
        readline();
        return $commission;
    }

    private function getTaxableAmount (Operation $operation)
    {
        if ($operation->getCurrency() == 'EUR')
        {
            $amountExceedingFreeCommissions = $operation->getAmount() - FREE_COMMISSION_PRIVATE_USER_WITHDRAW_AMOUNT;

            return $amountExceedingFreeCommissions >= 0 ? $amountExceedingFreeCommissions : 0;
        }
    }
}