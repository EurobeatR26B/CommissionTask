<?php

declare(strict_types=1);

namespace Justas\CommissionTask\Operation;

use DateTime;
use OperationType;

class Operation 
{
    public function __construct(
        DateTime $date,
        int $userID,
        string $userType,
        OperationType $operationType,
        float $amount,
        string $currency
    ){ }


}