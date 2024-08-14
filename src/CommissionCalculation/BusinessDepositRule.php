<?php

declare(strict_types=1);

namespace Justas\CommissionTask\CommissionCalculation;

use Justas\CommissionTask\Operation\Operation;
use Justas\CommissionTask\Operation\UserOperationTracker;

class BusinessDepositRule implements CommissionRuleInterface
{
    public function __construct()
    {
        
    }

    public function calculate(Operation $operation): float
    {
        $commissionRate = COMMISSION_BUSINESS_DEPOSIT;
        $commissionAmount = $operation->getAmount() * $commissionRate;
        $commissionAmount = round($commissionAmount, COMMISSION_ROUNDING_PRECISION);

        return $commissionAmount;
    }
}