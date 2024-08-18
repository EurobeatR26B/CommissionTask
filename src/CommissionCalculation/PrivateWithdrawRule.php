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
        $taxableOperationAmount = $this->getTaxableAmount($operation);

        $commission = $taxableOperationAmount * COMMISSION_PRIVATE_WITHDRAW;
        $commission = round($commission, COMMISSION_ROUNDING_PRECISION);

        if (in_array($operation->getCurrency(), CURRENCIES_WITH_NO_DECIMALS))
        {
            $commission = ceil($commission);
        }
        
        $this->userOperationTracker->addCompletedOperation($operation);

        return $commission;
    }

    private function getTaxableAmount (Operation $operation): float
    {
        $taxableAmount = $operation->getAmount();
        $isUserEligibleForFreeCommission = $this->userOperationTracker->isOperationEligibleForFreeCommission($operation);

        if ($isUserEligibleForFreeCommission)
        {
            if ($operation->getCurrency() == FREE_COMMISSION_PRIVATE_USER_WITHDRAW_CURRENCY)
            {
                // $taxableAmount = $operation->getAmount() - FREE_COMMISSION_PRIVATE_USER_WITHDRAW_AMOUNT;
        
                $remainingTaxCredits = FREE_COMMISSION_PRIVATE_USER_WITHDRAW_AMOUNT - $this->userOperationTracker->getUserOperationSumThisPeriod($operation);
                $taxableAmount = $operation->getAmount() - $remainingTaxCredits;

                // $taxableAmount = $operation->getAmount() - FREE_COMMISSION_PRIVATE_USER_WITHDRAW_AMOUNT;
                $taxableAmount = $taxableAmount >= 0 ? $taxableAmount : 0.00;
            
            }

            else if ($operation->getCurrency() !== FREE_COMMISSION_PRIVATE_USER_WITHDRAW_CURRENCY)
            {
                $operationAmountInMainCurrency = $this->userOperationTracker->currencyConverter->convertOperation(
                    $operation,
                    FREE_COMMISSION_PRIVATE_USER_WITHDRAW_CURRENCY
                );

                if ($operationAmountInMainCurrency > FREE_COMMISSION_PRIVATE_USER_WITHDRAW_AMOUNT)
                {
                    $taxableAmountInMainCurrency = $operationAmountInMainCurrency - FREE_COMMISSION_PRIVATE_USER_WITHDRAW_AMOUNT;
                    $taxableAmountInOperationCurrency = $taxableAmountInMainCurrency * 129.53;

                    $taxableAmount = $taxableAmountInOperationCurrency;
                }
                else 
                {
                    $taxableAmount = 0.00;
                }
            }
        }

        return $taxableAmount;
    }
}