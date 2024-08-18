<?php

declare (strict_types=1);

namespace Justas\CommissionTask\Operation;

use Justas\CommissionTask\FileInput\FileReader;
use Justas\CommissionTask\User\UserType;
use DateTime;

class OperationParser
{

    public function __construct() { }

    public function parseFile(FileReader $fileReader): OperationRepository
    {
        $operationRepository = new OperationRepository();

        foreach ($fileReader->getLine() as $line)
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

            $operationRepository->addOperation($operation);
        }

        return $operationRepository;
    }

    public function parseSingleLine (object $line): Operation
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

        return $operation;
    }
}