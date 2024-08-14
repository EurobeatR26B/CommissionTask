<?php

declare (strict_types=1);

namespace Justas\CommissionTask\Operation;

use Justas\CommissionTask\CurrencyConversion\CurrencyConverterInterface;
use Justas\CommissionTask\User\UserType;

class UserOperationTracker
{    
    public CurrencyConverterInterface $currencyConverter;
    private OperationRepository $userOperationsRepository;

    public function __construct()
    {
        $this->userOperationsRepository = new OperationRepository();
    }

    public function addCompletedOperation(Operation $operation)
    {
        $this->userOperationsRepository->addOperation($operation);
    }

    public function isOperationEligibleForFreeCommission(Operation $operation): bool
    {
        return 
        $operation->getOperationType() === OperationType::WITHDRAW &&
        $operation->getUserType() === UserType::PRIVATE &&
        ($this->getUserOperationCountThisPeriod($operation) <= FREE_COMMISSION_PRIVATE_WITHDRAW_OPERATION_COUNT_LIMIT) && 
        ($this->getUserOperationSumThisPeriod($operation) <= FREE_COMMISSION_PRIVATE_USER_WITHDRAW_AMOUNT);
    }

    public function getUserOperationCountThisPeriod(Operation $operation): int
    {
        $operations = $this->userOperationsRepository->
        getOperationsByUserAndPeriod(
            $operation->getUserID(), 
            $operation->getPeriodOfOperation()
        );

        return count($operations);
    }

    public function getUserOperationSumThisPeriod(Operation $operation): float
    {
        $operations = $this->userOperationsRepository->
        getOperationsByUserAndPeriod(
            $operation->getUserID(), 
            $operation->getPeriodOfOperation()
        );
        
        $total = 0;
        
        foreach ($operations as $operation)
        {
            if ($operation->getCurrency() !== "EUR")
            {
                // $amount = $this->currencyConverter->
                // convertCurrency(
                //     $operation->getAmount(), 
                //     $operation->getCurrency(), 
                //     "EUR");

                $amountAfterConversionToEUR = $operation->getAmount() + 1;
                $total += $amountAfterConversionToEUR;
            }
            else {
                $total += (int) $operation->getAmount();
            }
        }

        return $total;
    }
}