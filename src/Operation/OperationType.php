<?php

declare(strict_types=1);

namespace Justas\CommissionTask\Operation;

enum OperationType
{
    case WITHDRAW;
    case DEPOSIT;
}
