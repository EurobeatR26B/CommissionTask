<?php

declare(strict_types=1);

namespace Justas\CommissionTask\Operation;

use DateTime;
use Justas\CommissionTask\User\UserType;

class Operation 
{
    public function __construct(
        private DateTime $date,
        private int $userID,
        private UserType $userType,
        private OperationType $operationType,
        private float $amount,
        private string $currency
    ){ }

    public function getPeriodOfOperation(): int
    {
        $unixStartTime = strtotime('1970-01-01');
        $oneWeekInSeconds = 60 * 60 * 24 * FREE_COMMISSION_LIMIT_DAYS;

        $date = $this->date;
        $timestamp = strtotime($date->format('Y-m-d'));
        
        $weekNumber = ($timestamp - $unixStartTime) / $oneWeekInSeconds;
        $weekNumber = (int) round($weekNumber);

        return $weekNumber;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getUserID(): int
    {
        return $this->userID;
    }

    public function getUserType(): UserType
    {
        return $this->userType;
    }

    public function getOperationType(): OperationType
    {
        return $this->operationType;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function __toString(): string
    {
        return trim(sprintf("%s by %s user %d on %s for %s %s", $this->operationType->name, $this->userType->name, $this->userID, $this->date->format('Y-m-d'), $this->amount, $this->currency));
    }
}