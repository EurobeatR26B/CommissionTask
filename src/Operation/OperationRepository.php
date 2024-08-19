<?php

declare(strict_types=1);

namespace Justas\CommissionTask\Operation;

class OperationRepository
{
    private array $operationMap;

    public function __construct()
    {
        $this->operationMap = [];
    }

    public function addOperation (Operation $operation)
    {
        $this->operationMap[$operation->getUserID()][] = $operation; 
    }

    public function getOperationsByUser (int $userID): array
    {
        return $this->operationMap[$userID];
    }

    public function getOperationsByUserAndPeriod(int $userID, int $week, OperationType $operationType = null): array
    {
        if (!isset ($this->operationMap[$userID]))
        {
            return array();
        }

        $userOperationsThisWeek = [];

        foreach ($this->operationMap[$userID] as $operation)
        {
            if ($operation->getPeriodOfOperation() == $week)
            {
                $userOperationsThisWeek [] = $operation;
            }
        }

        if (isset($operationType))
        {
            $userOperationsThisWeek = array_filter ($userOperationsThisWeek, function (Operation $operation) use ($operationType)
            {
                return $operation->getOperationType() === $operationType;
            });
        }

        return $userOperationsThisWeek;
    } 

    public function getAll()
    {
        return $this->operationMap;
    }
}