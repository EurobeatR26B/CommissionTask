<?php

declare(strict_types=1);

namespace Justas\CommissionTask\CommissionCalculation;

use Justas\CommissionTask\Operation\Operation;
use Justas\CommissionTask\Operation\UserOperationTracker;

class PrivateDepositRule implements CommissionRuleInterface
{
    public function __construct()
    {

    }

    public function calculate(Operation $operation): float
    {
        $commissionRate = COMMISSION_PRIVATE_DEPOSIT;
        $taxableAmount = $this->getTaxableAmount($operation);

        $commissionAmount = $taxableAmount * $commissionRate;

        return $commissionAmount;
    }

    public function getTaxableAmount(Operation $operation): float
    {
        return $operation->getAmount();
    }
}
