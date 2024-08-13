<?php

declare (strict_types=1);

namespace Justas\CommissionTask\Operation;

class UserOperationTracker
{
    private OperationRepository $userOperations;

    public function __construct(
        private int $userID
    ){}


}