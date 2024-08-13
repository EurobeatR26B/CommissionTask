<?php

declare (strict_types=1);

namespace Justas\CommissionTask\Operation;

use Justas\CommissionTask\FileInput\FileReader;
use Justas\CommissionTask\User\UserType;
use DateTime;

class OperationParser
{
    private FileReader $fileReader;

    public function __construct(FileReader $fileReader) {
        $this->fileReader = $fileReader;
    }

    public function parseFile()
    {
        foreach ($this->fileReader->getLine() as $line)
        {
            $date = new DateTime($line->date);

            $userID = (int) $line->userID;

            $userType = match ($line->userType) {
                'private'  => UserType::PRIVATE,
                'business' => UserType::BUSINESS
            };

            $operationType = match ($line->operationType) {
                'withdraw'  => OperationType::WITHDRAW,
                'deposit' => OperationType::DEPOSIT
            };

            $amount = (float) $line->amount;

            $currency = $line->currency;

            $operation = new Operation(
                $date,
                $userID,
                $userType,
                $operationType,
                $amount,
                $currency
            );

            var_dump($operation);
        }
    }
}