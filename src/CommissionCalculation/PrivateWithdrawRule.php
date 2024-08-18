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

    public function getTaxableAmount (Operation $operation): float
    {
        $taxableAmount = $operation->getAmount();
        
        $userOperationSum = $this->userOperationTracker->getUserOperationSumThisPeriod($operation);
        $remainingTaxCredits = FREE_COMMISSION_PRIVATE_USER_WITHDRAW_AMOUNT - $userOperationSum;

        if ($operation->getCurrency() === FREE_COMMISSION_PRIVATE_USER_WITHDRAW_CURRENCY)
        {
            if ($remainingTaxCredits <= 0)
            {
                $taxableAmount = $operation->getAmount();
            }
            else 
            {
                $taxableAmount = $operation->getAmount() >= $remainingTaxCredits ?
                $operation->getAmount() - $remainingTaxCredits :
                0.00;
            }            

            return $taxableAmount;
        }
        else 
        {
            $exchangeRate = $this->userOperationTracker->currencyConverter->getExchangeRate($operation->getCurrency(), FREE_COMMISSION_PRIVATE_USER_WITHDRAW_CURRENCY);

            $operationInMainCurrency = $operation->getAmount() * $exchangeRate;
            if ($operationInMainCurrency > $remainingTaxCredits)
            {
                if ($remainingTaxCredits <= 0)
                {
                    $taxableAmount = $operation->getAmount();
                }
                else 
                {
                    $amountToTaxInMainCurrency = $operationInMainCurrency - $remainingTaxCredits;
                    $amountToTaxInOperationCurrency = $amountToTaxInMainCurrency / $exchangeRate;

                    $taxableAmount = $amountToTaxInOperationCurrency;
                }                
            }
            else 
            {
                $taxableAmount = 0.00;
            }            
        }

        return $taxableAmount;
    }
}