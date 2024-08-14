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

    public function calculate(Operation $operation, UserOperationTracker $operationTracker)
    {
        
    }
}