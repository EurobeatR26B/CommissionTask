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

    public function getTaxableAmount (Operation $operation): float
    {
        $taxableAmount = $operation->getAmount();
        
        $userOperationSum = $this->userOperationTracker->getUserOperationSumThisPeriod($operation);
        $remainingTaxCredits = FREE_COMMISSION_PRIVATE_USER_WITHDRAW_AMOUNT - $userOperationSum;

        $userOperationCount = $this->userOperationTracker->getUserOperationCountThisPeriod($operation) + 1;

        if ($operation->getCurrency() === FREE_COMMISSION_PRIVATE_USER_WITHDRAW_CURRENCY)
        {
            if ($userOperationCount > FREE_COMMISSION_PRIVATE_WITHDRAW_OPERATION_COUNT_LIMIT)
            {
                return $operation->getAmount();                
            }
            else 
            {
                $totalAmountDoneByUser = $userOperationSum + $operation->getAmount();

                if ($totalAmountDoneByUser <= FREE_COMMISSION_PRIVATE_USER_WITHDRAW_AMOUNT)
                {
                    return 0.00;
                }                
            }

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

            if ($userOperationCount <= FREE_COMMISSION_PRIVATE_WITHDRAW_OPERATION_COUNT_LIMIT)
            {
                $totalAmountDoneByUser = $userOperationSum + $operationInMainCurrency;

                if ($totalAmountDoneByUser <= FREE_COMMISSION_PRIVATE_USER_WITHDRAW_AMOUNT)
                {
                    return 0.00;
                }
            }
            else 
            {
                return $operation->getAmount();
            }

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