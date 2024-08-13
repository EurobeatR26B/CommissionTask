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

    public function getOperationsByUser (int $userID)
    {
        return $this->operationMap[$userID];
    }
}