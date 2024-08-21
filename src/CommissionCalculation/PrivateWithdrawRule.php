<?php

declare(strict_types=1);

namespace Justas\CommissionTask\CommissionCalculation;

use Justas\CommissionTask\CurrencyConversion\CurrencyConverterInterface;
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
        $taxableOperationAmount = $this->getTaxableAmount($operation);

        $commission = $taxableOperationAmount * COMMISSION_PRIVATE_WITHDRAW;

        $this->userOperationTracker->addCompletedOperation($operation);

        return $commission;
    }

    public function getTaxableAmount(Operation $operation): float
    {
        $userOperationSum = $this->userOperationTracker->getUserOperationSumThisPeriod($operation);
        $remainingTaxCredits = FREE_COMMISSION_PRIVATE_USER_WITHDRAW_AMOUNT - $userOperationSum;

        $userOperationCount = $this->userOperationTracker->getUserOperationCountThisPeriod($operation) + 1;

        if ($operation->getCurrency() === FREE_COMMISSION_PRIVATE_USER_WITHDRAW_CURRENCY) {
            return $this->calculateCommissionInMainCurrency($operation->getAmount(), $remainingTaxCredits, $userOperationSum, $userOperationCount);
        } else {
            return $this->calculateCommissionInOtherCurrency($operation->getCurrency(), $operation->getAmount(), $remainingTaxCredits, $userOperationSum, $userOperationCount);
        }

        return $operation->getAmount();
    }

    private function calculateCommissionInMainCurrency(float $operationAmount, float $remainingCredits, float $userOperationSum, int $userOperationCount)
    {
        if ($userOperationCount > FREE_COMMISSION_PRIVATE_WITHDRAW_OPERATION_COUNT_LIMIT) {
            return $operationAmount;
        } else {
            $totalAmountDoneByUser = $userOperationSum + $operationAmount;

            if ($totalAmountDoneByUser <= FREE_COMMISSION_PRIVATE_USER_WITHDRAW_AMOUNT) {
                return 0.00;
            }
        }

        if ($remainingCredits <= 0) {
            return $operationAmount;
        } else {
            return $operationAmount >= $remainingCredits ?
                $operationAmount - $remainingCredits :
                0.00;
        }
    }

    public function calculateCommissionInOtherCurrency(string $operationCurrency, float $operationAmount, float $remainingTaxCredits, float $userOperationSum, int $userOperationCount)
    {
        $exchangeRate = $this->userOperationTracker->currencyConverter->getExchangeRate($operationCurrency, FREE_COMMISSION_PRIVATE_USER_WITHDRAW_CURRENCY);

        $operationInMainCurrency = $operationAmount * $exchangeRate;

        if ($userOperationCount <= FREE_COMMISSION_PRIVATE_WITHDRAW_OPERATION_COUNT_LIMIT) {
            $totalAmountDoneByUser = $userOperationSum + $operationInMainCurrency;

            if ($totalAmountDoneByUser <= FREE_COMMISSION_PRIVATE_USER_WITHDRAW_AMOUNT) {
                return 0.00;
            }
        } else {
            return $operationAmount;
        }

        if ($operationInMainCurrency > $remainingTaxCredits) {
            if ($remainingTaxCredits <= 0) {
                return $operationAmount;
            } else {
                $amountToTaxInMainCurrency = $operationInMainCurrency - $remainingTaxCredits;
                $amountToTaxInOperationCurrency = $amountToTaxInMainCurrency / $exchangeRate;

                return $amountToTaxInOperationCurrency;
            }
        } else {
            return 0.00;
        }
    }
}
