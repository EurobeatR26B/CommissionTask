<?php

declare(strict_types=1);

namespace Justas\CommissionTask\CommissionCalculation;

use Justas\CommissionTask\Operation\Operation;
use Justas\CommissionTask\Operation\UserOperationTracker;

interface CommissionRuleInterface
{
    public function calculate(Operation $operation, UserOperationTracker $operationTracker);
}