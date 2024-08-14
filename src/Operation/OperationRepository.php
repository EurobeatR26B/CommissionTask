<?php

declare(strict_types=1);

namespace Justas\CommissionTask\Operation;

class OperationRepository
{
    private array $operationMap;

    public function __construct()
    {
        
    }

    public function addOperation (Operation $operation)
    {
        $this->operationMap[$operation->getUserID()][] = $operation; 
    }

    public function getOperationsByUser (int $userID): array
    {
        return $this->operationMap[$userID];
    }

    public function getOperationsByUserAndWeek(int $userID, int $week)
    {
        $userOperationsThisWeek = [];

        foreach ($this->operationMap[$userID] as $operation)
        {
            if ($operation->getWeekOfOperation() === $week)
            {
                $userOperationsThisWeek [] = $operation;
            }
        }

        return $userOperationsThisWeek;
    } 

    public function getAll()
    {
        return $this->operationMap;
    }
}