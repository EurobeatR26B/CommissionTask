<?php

declare (strict_types=1);

namespace Justas\CommissionTask\Operation;

use Justas\CommissionTask\CurrencyConversion;
use Justas\CommissionTask\CurrencyConversion\CurrencyConverterInterface;

class UserOperationTracker
{
    private OperationRepository $userOperationsRepository;
    private CurrencyConverterInterface $currencyConverter;

    public function __construct(
        private int $userID
    ){}


    public function isEligibleForFreeCommission(Operation $operation): bool
    {
        return 
        ($this->getUserOperationCountThisWeek($operation) <= 3 ) && 
        ($this->getUserOperationSumThisWeek($operation) <= 1000);
    }

    private function getUserOperationCountThisWeek(Operation $operation): int
    {
        $operations = $this->userOperationsRepository->
        getOperationsByUserAndWeek(
            $operation->getUserID(), 
            $operation->getWeekOfOperation()
        );

        return count($operations);
    }

    private function getUserOperationSumThisWeek(Operation $operation): float
    {
        $operations = $this->userOperationsRepository->
        getOperationsByUserAndWeek(
            $operation->getUserID(), 
            $operation->getWeekOfOperation()
        );
        
        $total = 0;

        foreach ($operations as $operation)
        {
            if ($operation->getCurrency !== "EUR")
            {
                $amount = $this->currencyConverter->
                convertCurrency(
                    $operation->getAmount(), 
                    $operation->getCurrency(), 
                    "EUR");

                $total += $amount;
            }
        }
        return $total;
    }
}