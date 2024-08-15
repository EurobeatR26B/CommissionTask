<?php

declare (strict_types=1);

namespace Justas\CommissionTask\CurrencyConversion;

use Justas\CommissionTask\Operation\Operation;

interface CurrencyConverterInterface
{
    public function getExchangeRate(string $startCurrency, string $endCurrency);
    public function convertCurrency (Operation $operation, string $currencyToConvertTo): float;
}