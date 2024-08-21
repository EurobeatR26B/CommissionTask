<?php

declare(strict_types=1);

namespace Justas\CommissionTask\CommissionCalculation;

use Justas\CommissionTask\CurrencyConversion\CurrencyConverterInterface;
use Justas\CommissionTask\Operation\Operation;
use Justas\CommissionTask\Operation\OperationType;
use Justas\CommissionTask\Operation\UserOperationTracker;
use Justas\CommissionTask\User\UserType;
use PHPUnit\Framework\Constraint\Operator;

class CommissionCalculator
{
    public function __construct(
        private UserOperationTracker $operationTracker,
    ) {
    }

    public function calculateCommission(Operation $operation)
    {
        $commissionRule = $this->getCommissionRule($operation);
        $commissionAmount = $commissionRule->calculate($operation, $this->operationTracker);

        $commissionAmount = $this->roundCommission($operation->getCurrency(), $commissionAmount);

        return $commissionAmount;
    }

    private function getCommissionRule(Operation $operation)
    {
        $commissionRule = match ($operation->getUserType()) {
            UserType::PRIVATE => match ($operation->getOperationType()) {
                OperationType::DEPOSIT => new PrivateDepositRule(),
                OperationType::WITHDRAW => new PrivateWithdrawRule($this->operationTracker)
            },
            UserType::BUSINESS => match ($operation->getOperationType()) {
                OperationType::DEPOSIT => new BusinessDepositRule(),
                OperationType::WITHDRAW => new BusinessWithdrawRule()
            },
        };

        return $commissionRule;
    }

    private function roundCommission(string $currency, float $amount)
    {
        if (in_array($currency, CURRENCIES_WITH_NO_DECIMALS)) {
            return ceil($amount);
        }

        if ($amount < 0.10) {
            return $amount;
        }

        $rounded = round($amount, COMMISSION_ROUNDING_PRECISION);
        return $rounded;
    }
}
